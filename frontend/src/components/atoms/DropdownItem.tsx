import { Anchor } from './Anchor';
import ListItems from './ListItems';

interface DropdownItemProps {
	children: React.ReactNode;
	href: string;
}

const DropdownItem = ({ href, children }: DropdownItemProps) => (
	<ListItems>
		<Anchor
			href={href}
			className='flex items-center justify-center'
		>
			{children}
		</Anchor>
	</ListItems>
);

export default DropdownItem;
