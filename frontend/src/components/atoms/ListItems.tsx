import React, { ComponentProps } from "react";
import { twMerge } from "tailwind-merge";

export type ListItemsProps = ComponentProps<"li"> & {
  children?: React.ReactNode;
  merge?: boolean;
};

const ListItems = ({
  children,
  merge = false,
  className,
  ...props
}: ListItemsProps) => {
  return (
    <li className={merge ? twMerge("", className) : className} {...props}>
      {children}
    </li>
  );
};

export default ListItems;
