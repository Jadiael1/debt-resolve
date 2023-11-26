import { useState } from "react";
import Logo from "../atoms/Logo";
import NavButton from "../atoms/NavButton";
import NavMenuLeft from "../molecules/NavMenuLeft";
import NavMenuRight from "../molecules/NavMenuRight";
import NavMenuReduced from "../molecules/NavMenuReduced";

const Navbar = () => {
  const [toggleMenu, setToggleMenu] = useState<string>("hidden");
  const handleClickMenu = () => {
    setToggleMenu((currentValue) => (currentValue == "hidden" ? "" : "hidden"));
  };
  return (
    <nav className="relative flex items-center py-2 bg-gradient-to-r from-gray-700 to-gray-900 text-white">
      <div className="flex w-full px-2 my-auto flex-wrap justify-between">
        <Logo />
        <NavButton handleClickMenu={handleClickMenu} />
        <div className="hidden sm:flex basis-auto items-center grow transition-all ease-in-out duration-300">
          <NavMenuLeft />
          <NavMenuRight />
        </div>
        <div className={`${toggleMenu} basis-full grow sm:hidden ml-1 mt-1`}>
          <NavMenuReduced />
        </div>
      </div>
    </nav>
  );
};

export default Navbar;
