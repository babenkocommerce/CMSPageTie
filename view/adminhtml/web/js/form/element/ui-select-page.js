define([
    'Magento_Ui/js/form/element/ui-select',
    'underscore'
], function (UiSelect, _) {
    return UiSelect.extend({
        defaults: {
            listens: {
                '${ $.provider }:data.store_id': 'onPageStoreIdChange'
            }
        },

        /**
         * @inheritDoc
         */
        initialize: function() {
            this._super();
            this.setDisabled();
        },

        /**
         * Listen to changes of selected StoreViews
         * @param selectedStoreViews
         */
        onPageStoreIdChange: function (selectedStoreViews) {
            this.setDisabled(selectedStoreViews);
        },

        /**
         * Disable component if it's storeView is used by current CMS Page
         * or if current CMS Page has 'All Store Views' selected
         * @param selectedStoreViews
         */
        setDisabled: function (selectedStoreViews) {
            var elementStoreView = this.source.get(this.parentScope + '.store_id'),
                storeViews = selectedStoreViews ? selectedStoreViews : this.source.get('data.store_id');

            if (
                _.contains(storeViews, '0') ||
                _.contains(storeViews, elementStoreView)
            ) {
                this.disable();
            } else {
                this.enable();
            }
        },

        /**
         * Toggle list visibility if component is enabled
         *
         * @returns {Object} Chainable
         */
        toggleListVisible: function () {
            if (!this.disabled()) {
                this.listVisible(!this.listVisible());
            }

            return this;
        },
    });
});
