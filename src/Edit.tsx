/**
 * WordPress Dependencies
 */
import { useRef, useEffect, useMemo, useState } from '@wordpress/element';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';

/**
 * External Dependencies
 */
import React, { FC } from 'react';
import Select, { MultiValue } from 'react-select';

/**
 * Internal Dependencies
 */
import { useBlockClassOptions } from './hooks/useBlockClassOptions';
import { BlockEditProps } from './types';
import styles from './edit.module.scss';

type BlockAttributes = {
	className: string;
};

type PresetOption = {
	label: string;
	value: string;
};

const MAX_BUTTON_OPTIONS = 10;

const sortedSetValues = ( set: Set< string > ): string[] => {
	return Array.from( set ).sort();
};

const areSetsEqual = ( a: Set< string >, b: Set< string > ): boolean => {
	if ( a.size !== b.size ) {
		return false;
	}

	const aValues = sortedSetValues( a );
	const bValues = sortedSetValues( b );

	return aValues.every( ( value, index ) => value === bValues[ index ] );
};

export const Edit = createHigherOrderComponent<
	FC< BlockEditProps< BlockAttributes > >,
	React.ComponentType< BlockEditProps< BlockAttributes > >
>( ( BlockEdit ) => {
	return ( props: BlockEditProps< BlockAttributes > ) => {
		const { attributes, setAttributes, isSelected, name } = props;
		const { className = '' } = attributes;
		const [ blockClasses, setBlockClasses ] = useState< Set< string > >(
			new Set()
		);
		const presetOptions = useBlockClassOptions(
			name,
			attributes as Record< string, unknown >
		);
		const currentBlockClasses = useRef( new Set< string >() );
		const presetOptionValues = useMemo( () => {
			return new Set( presetOptions.map( ( { value } ) => value ) );
		}, [ presetOptions ] );
		const selectedPresetOptions = useMemo( () => {
			return presetOptions.filter( ( { value } ) =>
				blockClasses.has( value )
			);
		}, [ blockClasses, presetOptions ] );

		useEffect( () => {
			const classSet = new Set(
				( className || '' )
					.split( /\s+/ )
					.map( ( value ) => value.trim() )
					.filter( Boolean )
			);

			if ( ! areSetsEqual( classSet, currentBlockClasses.current ) ) {
				setBlockClasses( classSet );
			}
		}, [ className ] );

		useEffect( () => {
			if ( ! areSetsEqual( currentBlockClasses.current, blockClasses ) ) {
				currentBlockClasses.current = new Set( blockClasses );
				setAttributes( {
					className: Array.from( blockClasses ).join( ' ' ),
				} );
			}
		}, [ blockClasses, setAttributes ] );

		const addClass = ( value: string ) => {
			const classes = new Set( blockClasses );
			classes.add( value );
			setBlockClasses( classes );
		};

		const removeClass = ( value: string ) => {
			const classes = new Set( blockClasses );
			classes.delete( value );
			setBlockClasses( classes );
		};

		const togglePresetClass = ( value: string, optionState: boolean ) => {
			if ( ! optionState ) {
				removeClass( value );
				return;
			}

			addClass( value );
		};

		const setPresetClasses = ( values: MultiValue< PresetOption > ) => {
			const classes = new Set( blockClasses );

			presetOptionValues.forEach( ( value ) => {
				classes.delete( value );
			} );

			values.forEach( ( { value } ) => {
				classes.add( value );
			} );

			setBlockClasses( classes );
		};

		if ( ! presetOptions.length ) {
			return <BlockEdit { ...props } />;
		}

		return (
			<>
				<BlockEdit { ...props } />

				{ isSelected && (
					<InspectorControls>
						<PanelBody title="Block Presets" initialOpen={ false }>
							{ presetOptions.length > MAX_BUTTON_OPTIONS ? (
								<div className={ styles[ 'select-container' ] }>
									<Select
										aria-label="Block presets"
										classNamePrefix="block-preset-classes-select"
										closeMenuOnSelect={ false }
										hideSelectedOptions={ false }
										isClearable
										isMulti
										options={ presetOptions }
										placeholder="Select presets..."
										value={ selectedPresetOptions }
										onChange={ setPresetClasses }
									/>
								</div>
							) : (
								<div className={ styles[ 'button-container' ] }>
									{ presetOptions.map(
										( { label, value } ) => {
											const isActive =
												blockClasses.has( value );

											return (
												<Button
													key={ value }
													onClick={ () =>
														togglePresetClass(
															value,
															! isActive
														)
													}
													isPressed={ isActive }
													variant="secondary"
												>
													{ label }
												</Button>
											);
										}
									) }
								</div>
							) }
						</PanelBody>
					</InspectorControls>
				) }
			</>
		);
	};
}, 'Edit' );
