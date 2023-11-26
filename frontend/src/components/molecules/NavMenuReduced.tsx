import ListItems from "../atoms/ListItems";
import NavLink from "../atoms/NavLink";
import UserDropdownReduced from "./UserDropdownReduced";

const NavMenuReduced = () => (
  <ul className="mb-2 mr-auto flex flex-col pl-0 list-none">
    <NavLink className="list-item hover:bg-white hover:bg-opacity-10" href="./">
      Home
    </NavLink>
    <NavLink className="list-item hover:bg-white hover:bg-opacity-10" href="./">
      Contato
    </NavLink>
    <ListItems className="border-t border-white my-1"></ListItems>
    <UserDropdownReduced />
  </ul>
);

export default NavMenuReduced;
