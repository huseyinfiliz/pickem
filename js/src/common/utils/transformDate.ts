import Model from 'flarum/common/Model';

export default function transformDate(value: unknown): Date | null {
  return typeof value === 'string' ? Model.transformDate(value) : null;
}
