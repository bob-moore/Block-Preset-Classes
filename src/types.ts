export type BlockEditProps< T = Record< string, unknown > > = {
	attributes: T;
	setAttributes: ( attrs: Partial< T > ) => void;
	clientId: string;
	isSelected: boolean;
	context: Record< string, unknown >;
	name: string;
	insertBlocksAfter?: ( blocks: unknown[] ) => void;
	onReplace?: ( blocks: unknown[] ) => void;
	mergeBlocks?: ( forward: boolean ) => void;
	__unstableLayoutClassNames?: string;
	toggleSelection?: ( value?: boolean ) => void;
};
