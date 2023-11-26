import Avatar from "../atoms/Avatar";
import ListItems from "../atoms/ListItems";
import UserDropdown from "./UserDropdown";

const NavMenuRight = () => (
  <ul className="flex flex-row ml-auto list-none items-center space-x-4">
    <ListItems className="relative group">
      <div className="flex space-x-2 hover:bg-white hover:bg-opacity-10 hover:rounded transition-all ease-in-out duration-300 cursor-pointer">
        <span className="ml-1">User</span>
        <Avatar />
      </div>
      <UserDropdown />
    </ListItems>
  </ul>
);

export default NavMenuRight;
