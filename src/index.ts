import { addFilter } from '@wordpress/hooks';
import { Edit } from './Edit';

addFilter( 'editor.BlockEdit', 'bmd/blockPresets', Edit );

// addFilter(
// 	'bmd.blockPresets',
// 	'bmd/blockPresets/debug-class-options',
// 	( options, blockName, blockAttributes ) => {
// 		console.log( '[bmd.blockPresets.classOptions]', {
// 			blockName,
// 			blockAttributes,
// 			options,
// 		} );

// 		// Return the original options by default.
// 		return options;
// 	}
// );
