wp.hooks.addFilter(
    'blocks.registerBlockType',
    'my-theme/modify-attributes',
    function (settings, name) {
        if (name !== 'core/image') {
            return settings;
        }

        return lodash.assign({}, settings, {
            attributes: lodash.assign({}, settings.attributes, {
                sizes: {
                    type: 'string',
                    default: 'my-custom-size'
                },
            }),
        });
    }
);
