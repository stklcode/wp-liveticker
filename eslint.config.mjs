import wordpress from '@wordpress/eslint-plugin';
import globals from 'globals';

export default [
	...wordpress.configs.recommended.map( ( config ) => ( {
		...config,
		files: [ 'scripts/block.js' ],
	} ) ),

	...wordpress.configs.es5.map( ( config ) => ( {
		...config,
		files: [ 'scripts/liveticker.js' ],
	} ) ),

	...wordpress.configs.i18n,
	...wordpress.configs.jsdoc,
	{
		languageOptions: {
			globals: {
				...globals.browser,
				scliveticker: 'readonly',
				wp: 'readonly',
			},
		},
		rules: {
			'@wordpress/i18n-text-domain': [
				'error',
				{
					allowedTextDomain: [ 'stklcode-liveticker' ],
				},
			],
			'no-unused-vars': 'warn',
		},
	},
];
