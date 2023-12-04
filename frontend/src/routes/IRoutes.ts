export default interface IRoutes {
	path: string;
	visibleInDisplay?: boolean;
	displayName?: string;
	protected: boolean;
	component: () => JSX.Element;
}
