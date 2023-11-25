import React from "react";
import Input from "../atoms/Input";
import Button from "../atoms/Button";

const SearchBar: React.FC = () => {
  return (
    <div>
      <Input />
      <Button>Buscar</Button>
    </div>
  );
};

export default SearchBar;
