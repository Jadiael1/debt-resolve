import React, { ComponentProps } from 'react';
import { twMerge } from 'tailwind-merge';

export type AnchorProps = ComponentProps<'a'> & {
	children: React.ReactNode;
	merge?: boolean;
};

export function Anchor({ children, merge = false, className, ...props }: AnchorProps) {
	return (
		<a
			className={merge ? twMerge('text-gray-800 hover:text-blue-500 px-4 py-2', className) : className}
			{...props}
		>
			{children}
		</a>
	);
}
