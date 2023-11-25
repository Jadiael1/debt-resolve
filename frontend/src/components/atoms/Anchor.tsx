import React, { ComponentProps } from "react";
import { twMerge } from "tailwind-merge";

export type AnchorProps = ComponentProps<"a"> & {
  children: React.ReactNode;
  replace: boolean;
};

export function Anchor({
  children,
  replace = false,
  className,
  ...props
}: AnchorProps) {
  return (
    <a
      className={
        replace
          ? className
          : twMerge("text-gray-800 hover:text-blue-500 px-4 py-2", className)
      }
      {...props}
    >
      {children}
    </a>
  );
}
