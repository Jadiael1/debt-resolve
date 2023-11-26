import ListItems from "../atoms/ListItems";
import NavLinkReduced from "../atoms/NavLinkReduced";
import UserDropdownReduced from "./UserDropdownReduced";

const NavMenuReduced = () => (
  <ul className="mb-2 mr-auto flex flex-col pl-0 list-none">
    <NavLinkReduced href="./">Home</NavLinkReduced>
    <NavLinkReduced href="./">Contato</NavLinkReduced>
    <ListItems className="border-t border-white my-1"></ListItems>
    <UserDropdownReduced />
  </ul>
);

export default NavMenuReduced;
