import { Anchor } from "./Anchor";
import ListItems from "./ListItems";

interface NavLinkReducedProps {
  children: React.ReactNode;
  href: string;
}

const NavLinkReduced = ({ href, children }: NavLinkReducedProps) => (
  <ListItems className="list-item hover:bg-white hover:bg-opacity-10">
    <Anchor href={href} className="hover:text-gray-300">
      {children}
    </Anchor>
  </ListItems>
);

export default NavLinkReduced;
