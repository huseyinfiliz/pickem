import commonExtenders from '../common/extend';
import Extend from 'flarum/common/extenders';
import app from 'flarum/admin/app';
import PickemPage from './components/PickemPage';

export default [
  ...commonExtenders,
  new Extend.Admin('huseyinfiliz-pickem')
    .page(PickemPage)
    .permission(
      () => ({ icon: 'fas fa-trophy', label: app.translator.trans('huseyinfiliz-pickem.admin.permissions.manage'), permission: 'pickem.manage' }),
      'moderate'
    )
    .permission(
      () => ({
        icon: 'fas fa-check-circle',
        label: app.translator.trans('huseyinfiliz-pickem.admin.permissions.make_picks'),
        permission: 'pickem.makePicks',
      }),
      'start'
    )
    .permission(
      () => ({
        icon: 'fas fa-eye',
        label: app.translator.trans('huseyinfiliz-pickem.admin.permissions.view_page'),
        permission: 'pickem.view',
        allowGuest: true,
      }),
      'view'
    ),
];
