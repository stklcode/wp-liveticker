import wordpress from '@wordpress/eslint-plugin';
import globals from 'globals';

export default [
	...wordpress.configs.custom,
	...wordpress.configs.es5,
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
