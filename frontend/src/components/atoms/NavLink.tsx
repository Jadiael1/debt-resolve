import { Anchor } from "./Anchor";
import ListItems from "./ListItems";
import { twMerge } from "tailwind-merge";

interface NavLinkProps {
  children: React.ReactNode;
  href: string;
  merge?: boolean;
  className?: string;
}

const NavLink = ({
  href,
  children,
  merge = false,
  className = "text-center",
}: NavLinkProps) => (
  <ListItems className={merge ? twMerge("text-center") : className}>
    <Anchor
      href={href}
      className="hover:text-gray-300 transition-colors duration-300"
    >
      {children}
    </Anchor>
  </ListItems>
);

export default NavLink;
