import React, { ComponentProps } from "react";
import { twMerge } from "tailwind-merge";

export type ButtonProps = ComponentProps<"button"> & {
  children: React.ReactNode;
  merge?: boolean;
};

const Button: React.FC<ButtonProps> = ({
  children,
  merge = false,
  className,
  ...props
}: ButtonProps) => {
  return (
    <button className={merge ? twMerge("", className) : className} {...props}>
      {children}
    </button>
  );
};

export default Button;
