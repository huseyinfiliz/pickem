import IndexSidebar from 'flarum/forum/components/IndexSidebar';
import app from 'flarum/forum/app';
import { extend as extendPrototype } from 'flarum/common/extend';
import LinkButton from 'flarum/common/components/LinkButton';
import commonExtenders from '../common/extend';
export const extend = commonExtenders;
import PickemPage from './components/PickemPage';
import EventResultNotification from './components/EventResultNotification';

app.initializers.add('huseyinfiliz/pickem', () => {
  app.routes.pickem = { path: '/pickem', component: PickemPage };

  extendPrototype(IndexSidebar.prototype, 'navItems', function (items) {
    if (app.forum.attribute('pickem.canView')) {
      items.add(
        'pickem',
        LinkButton.component(
          {
            href: app.route('pickem'),
            icon: 'fas fa-trophy',
          },
          app.translator.trans('huseyinfiliz-pickem.lib.nav.pickem')
        ),
        85
      );
    }
  });

  app.notificationComponents.pickem_event_result = EventResultNotification;
});
