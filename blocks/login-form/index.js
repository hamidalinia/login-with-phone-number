//blocks/login-form/index.js
( function ( blocks, element, blockEditor, components, i18n ) {
    var el = element.createElement;
    var __ = i18n.__;

    blocks.registerBlockType( 'idehweb/login-form', {
        edit: function ( props ) {
            return el(
                components.Placeholder,
                {
                    icon: 'smartphone',
                    label: __( 'OTP Login Form', 'login-with-phone-number' ),
                },
                el( 'p', {}, __( 'The phone number login/register form will appear here on the frontend.', 'login-with-phone-number' ) )
            );
        },
        save: function () {
            return null; // render_callback در PHP هندلش می‌کنه (dynamic block)
        },
    } );
} )(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n
);