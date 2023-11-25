export default interface IRoutes {
  path: string;
  visibleInDisplay?: boolean;
  displayName?: string;
  component: () => JSX.Element;
}
