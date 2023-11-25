import React from "react";
import { Anchor } from "../atoms/Anchor";

const Header: React.FC = () => {
  return (
    <header className="flex justify-between items-center py-4 bg-white shadow-md">
      <div className="flex items-center">
        <Anchor
          replace={true}
          href="/"
          className="text-xl font-bold text-gray-800 hover:text-gray-600"
        >
          DebtResolve
        </Anchor>
      </div>

      <nav className="flex">
        <Anchor replace={false} href="/signin">
          Login
        </Anchor>
        <Anchor
          replace={true}
          href="/signup"
          className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
        >
          Registrar-se
        </Anchor>
      </nav>
    </header>
  );
};

export default Header;
