import Model from 'flarum/common/Model';
import transformDate from '../utils/transformDate';

export default class Season extends Model {
  name = Model.attribute<string>('name');
  slug = Model.attribute<string>('slug');
  startDate = Model.attribute<Date | null>('startDate', transformDate);
  endDate = Model.attribute<Date | null>('endDate', transformDate);
}
