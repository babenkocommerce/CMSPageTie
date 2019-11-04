define([
    'Magento_Ui/js/form/element/ui-select',
    'underscore',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function (UiSelect, _, confirm, alert, $t) {
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

        /**
         * @inheritDoc
         * @param data
         * @returns {exports|*}
         */
        toggleOptionSelected: function (data) {
            if (this.isSelected(data.value)) {
                this.listVisible(false);
                return this;
            }
            if (data.available_in && data.available_in.length > 1) {
                if (_.intersection(data.available_in, this.source.get('data.store_id')).length) {
                    alert({
                        content: $t('The page you are trying to select also belongs ' +
                            'to Store of current page and therefore cannot be selected.')
                    });
                } else {
                    confirm({
                        content: $t('Selected page also belongs to other Stores, ' +
                            'select it for those Stores accordingly?'),
                        actions: {
                            confirm: this.onConfirm.bind(this, data)
                        }
                    });
                }
                return this;
            }
            return this._super();
        },

        /**
         * Get Cms Page Tie rows data indexed by store_id
         * @returns {Object}
         */
        getIndexedOptions: function () {
            if (!this.source.data.indexed_cms_page_tie_rows) {
                this.source.set('data.indexed_cms_page_tie_rows',
                    _.indexBy(this.source.data.cms_page_tie_rows, 'store_id')
                );
            }
            return this.source.data.indexed_cms_page_tie_rows;
        },

        /**
         * Select page for all Stores it belongs to
         * @param data
         */
        onConfirm: function (data) {
            var self = this;
            _.forEach(data.available_in, function (store_id) {
                self.source.set(
                    'data.cms_page_tie_rows.' +
                    self.getIndexedOptions()[store_id].record_id +
                    '.linked_page_id',
                    data.value
                )
            });
        }
    });
});
