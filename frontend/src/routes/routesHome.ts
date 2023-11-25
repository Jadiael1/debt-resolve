import HomeList from "../components/pages/Home/list";
import IRoutes from "./IRoutes";

const routesHome: IRoutes[] = [
  {
    path: "/",
    component: HomeList,
    visibleInDisplay: true,
    displayName: "Home",
  },
];

export default routesHome;
