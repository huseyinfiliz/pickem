<?php

namespace HuseyinFiliz\Pickem\Tests\Integration;

use Carbon\Carbon;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;
use HuseyinFiliz\Pickem\Event;
use HuseyinFiliz\Pickem\Pick;
use HuseyinFiliz\Pickem\Team;
use HuseyinFiliz\Pickem\UserScore;
use PHPUnit\Framework\Attributes\Test;

class PickemApiTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension('huseyinfiliz-pickem');
        $this->prepareDatabase([
            User::class => [$this->normalUser()],
            Team::class => [
                ['id' => 1, 'name' => 'Home', 'slug' => 'home'],
                ['id' => 2, 'name' => 'Away', 'slug' => 'away'],
            ],
            Event::class => [[
                'id' => 1, 'home_team_id' => 1, 'away_team_id' => 2,
                'match_date' => Carbon::now()->addDays(2),
                'cutoff_date' => Carbon::now()->addDay(),
                'allow_draw' => true,
            ]],
            'group_permission' => [
                ['permission' => 'pickem.view', 'group_id' => 2],
                ['permission' => 'pickem.makePicks', 'group_id' => 3],
            ],
        ]);
    }

    private function api(string $method, string $path, int $actor = 1, array $attributes = [])
    {
        return $this->send($this->request($method, '/api'.$path, [
            'authenticatedAs' => $actor,
            'json' => ['data' => ['attributes' => $attributes]],
        ]));
    }

    #[Test]
    public function eventsSerializeTeamsAndPaginate(): void
    {
        $response = $this->api('GET', '/pickem-events?page[limit]=1');
        $this->assertSame(200, $response->getStatusCode());
        $payload = json_decode((string) $response->getBody(), true);
        $this->assertTrue($payload['data'][0]['attributes']['canPick']);
        $this->assertSame('pickem-teams', $payload['data'][0]['relationships']['homeTeam']['data']['type']);
        $this->assertCount(2, $payload['included']);
        $this->assertSame(1, $payload['meta']['total']);
    }

    #[Test]
    public function memberCanPickAndListNestedIncludes(): void
    {
        $response = $this->api('POST', '/pickem-picks', 2, ['eventId' => 1, 'selectedOutcome' => 'home']);
        $this->assertSame(201, $response->getStatusCode(), (string) $response->getBody());
        $response = $this->api('GET', '/pickem-picks?include=event,event.homeTeam,event.awayTeam', 2);
        $this->assertSame(200, $response->getStatusCode(), (string) $response->getBody());
        $payload = json_decode((string) $response->getBody(), true);
        $this->assertCount(1, $payload['data']);
        $this->assertContains('pickem-events', array_column($payload['included'], 'type'));
        $this->assertContains('users', array_column($payload['included'], 'type'));
    }

    #[Test]
    public function resultsScorePicksAndSendNotifications(): void
    {
        $this->api('POST', '/pickem-picks', 2, ['eventId' => 1, 'selectedOutcome' => 'home']);
        $response = $this->api('POST', '/pickem-events/1/result', 1, ['homeScore' => 2, 'awayScore' => 1]);
        $this->assertSame(200, $response->getStatusCode(), (string) $response->getBody());
        $this->assertTrue(Pick::where('user_id', 2)->firstOrFail()->is_correct);
        $this->assertEquals(1, UserScore::where('user_id', 2)->firstOrFail()->total_points);
        $this->assertTrue($this->database()->table('notifications')->where('type', 'pickem_event_result')->where('user_id', 2)->exists());
        $this->assertSame(422, $this->api('POST', '/pickem-picks', 2, ['eventId' => 1, 'selectedOutcome' => 'away'])->getStatusCode());
    }

    #[Test]
    public function membersCannotManageAndInvalidMatchesAreRejected(): void
    {
        $this->assertSame(403, $this->api('POST', '/pickem-teams', 2, ['name' => 'Denied'])->getStatusCode());
        $response = $this->api('POST', '/pickem-events', 1, [
            'homeTeamId' => 1, 'awayTeamId' => 1,
            'matchDate' => Carbon::now()->addDays(2)->toIso8601String(),
            'cutoffDate' => Carbon::now()->addDay()->toIso8601String(),
        ]);
        $this->assertSame(422, $response->getStatusCode());
    }

    #[Test]
    public function partialMatchUpdatesPreserveValidationContext(): void
    {
        $response = $this->api('PATCH', '/pickem-events/1', 1, ['allowDraw' => false]);
        $this->assertSame(200, $response->getStatusCode(), (string) $response->getBody());
        $payload = json_decode((string) $response->getBody(), true);
        $this->assertFalse($payload['data']['attributes']['allowDraw']);
        $this->assertSame(422, $this->api('POST', '/pickem-picks', 2, ['eventId' => 1, 'selectedOutcome' => 'draw'])->getStatusCode());
    }

    #[Test]
    public function recalculateToolAndDeletingMatchesRefreshScores(): void
    {
        $this->api('POST', '/pickem-picks', 2, ['eventId' => 1, 'selectedOutcome' => 'home']);
        $this->api('POST', '/pickem-events/1/result', 1, ['homeScore' => 2, 'awayScore' => 1]);
        $response = $this->api('POST', '/pickem/recalculate-all-scores');
        $this->assertSame(200, $response->getStatusCode(), (string) $response->getBody());
        $this->assertSame(1, json_decode((string) $response->getBody(), true)['jobCount']);
        $this->assertSame(204, $this->api('DELETE', '/pickem-events/1')->getStatusCode());
        $this->assertSame(0, Pick::where('user_id', 2)->count());
        $this->assertEquals(0, UserScore::where('user_id', 2)->firstOrFail()->total_points);
    }
}
