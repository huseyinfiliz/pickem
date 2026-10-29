import Extend from 'flarum/common/extenders';
import Team from './models/Team';
import Season from './models/Season';
import Week from './models/Week';
import PickemEvent from './models/Event';
import Pick from './models/Pick';
import UserScore from './models/UserScore';

export default [
  new Extend.Store()
    .add('pickem-teams', Team)
    .add('pickem-seasons', Season)
    .add('pickem-weeks', Week)
    .add('pickem-events', PickemEvent)
    .add('pickem-picks', Pick)
    .add('pickem-user-scores', UserScore),
];
