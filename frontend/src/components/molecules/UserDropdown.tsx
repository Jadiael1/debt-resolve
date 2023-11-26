import DropdownItem from "../atoms/DropdownItem";
import ListItems from "../atoms/ListItems";

const UserDropdown = () => (
  <ul className="hidden group-hover:block rounded-md absolute top-full right-0 bg-gray-900 text-white py-2 px-4 transition-all ease-in-out duration-300">
    <DropdownItem href="#0">
      <span className="w-2 h-2 mr-2 bg-green-500 rounded-full"></span>
      <span>Dashboard</span>
    </DropdownItem>
    <ListItems className="border-t border-gray-700 my-2"></ListItems>
    <DropdownItem href="#1">
      <span className="w-2 h-2 mr-2 bg-red-500 rounded-full"></span>
      <span>Sair</span>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="16"
        height="16"
        fill="white"
        className="ml-2"
        viewBox="0 0 16 16"
      >
        <path
          fillRule="evenodd"
          d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z"
        ></path>
        <path
          fillRule="evenodd"
          d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"
        ></path>
      </svg>
    </DropdownItem>
  </ul>
);

export default UserDropdown;
