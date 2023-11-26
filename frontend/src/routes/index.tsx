import { BrowserRouter, Routes, Route } from "react-router-dom";
import routes from "./routes";

function AppRoutes() {
  return (
    <BrowserRouter>
      <Routes>
        {routes.map((route, key) => (
          <Route key={key} path={route.path} element={<route.component />} />
        ))}
      </Routes>
    </BrowserRouter>
  );
}

export default AppRoutes;
