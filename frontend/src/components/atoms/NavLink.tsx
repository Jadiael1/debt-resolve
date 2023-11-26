import { Anchor } from "./Anchor";
import ListItems from "./ListItems";

interface NavLinkProps {
  children: React.ReactNode;
  href: string;
}

const NavLink = ({ href, children }: NavLinkProps) => (
  <ListItems className="text-center">
    <Anchor
      href={href}
      className="hover:text-gray-300 transition-colors duration-300"
    >
      {children}
    </Anchor>
  </ListItems>
);

export default NavLink;
