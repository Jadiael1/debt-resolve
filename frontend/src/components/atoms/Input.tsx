import React from "react";

interface InputProps {
  placeholder?: string;
}

const Input: React.FC<InputProps> = ({ placeholder }) => {
  return <input type="text" placeholder={placeholder} />;
};

export default Input;
