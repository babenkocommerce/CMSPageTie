define([
    'Magento_Ui/js/form/element/ui-select',
    'underscore',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function (UiSelect, _, confirm, alert, $t) {
    return UiSelect.extend({
        defaults: {
            indexedOptions: false,
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
            if (this.value() !== '0' && this.getIndexedOptions()[this.value()].available_in.length > 1) {
                confirm({
                    content: $t('Currently linked page is also linked to other Stores. ' +
                        'If you continue, it will be unlinked from those Stores accordingly. Are you sure?'),
                    actions: {
                        confirm: this.onConfirmUnlink.bind(
                            this,
                            this.getIndexedOptions()[this.value()].available_in,
                            data
                        )
                    }
                });
                return this;
            } else if (data.available_in && data.available_in.length > 1) {
                this.maybeLink(data);
                return this;
            } else {
                return this._super();
            }
        },

        /**
         * Get options indexed by value
         * @returns {Object}
         */
        getIndexedOptions: function() {
            if (!this.indexedOptions) {
                this.indexedOptions = _.indexBy(this.options(),'value');
            }
            return this.indexedOptions;
        },

        /**
         * Get Cms Page Tie rows data indexed by store_id
         * @returns {Object}
         */
        getIndexedRows: function () {
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
        onConfirmLink: function (data) {
            var self = this;
            _.forEach(data.available_in, function (store_id) {
                self.source.set(
                    'data.cms_page_tie_rows.' +
                    self.getIndexedRows()[store_id].record_id +
                    '.linked_page_id',
                    data.value
                )
            });
        },

        /**
         * Unlink page, previously linked to many Stores
         * @param unlinkData
         * @param linkData
         */
        onConfirmUnlink: function (unlinkData, linkData) {
            var self = this;
            _.forEach(unlinkData, function (store_id) {
                self.source.set(
                    'data.cms_page_tie_rows.' +
                    self.getIndexedRows()[store_id].record_id +
                    '.linked_page_id',
                    '0'
                )
            });
            if (linkData.available_in && linkData.available_in.length > 1) {
                this.maybeLink(linkData);
            } else {
                this.value(linkData.value);
            }
        },

        /**
         * Check if selected page can be linked and ask user in popup
         * @param data
         */
        maybeLink: function (data) {
            if (_.intersection(data.available_in, this.source.get('data.store_id')).length) {
                alert({
                    content: $t('The page you are trying to select also belongs ' +
                        'to Store of current page and therefore cannot be selected.')
                });
            } else {
                confirm({
                    content: $t('The page you are trying to select also belongs to other Stores. ' +
                        'Select it for those Stores accordingly?'),
                    actions: {
                        confirm: this.onConfirmLink.bind(this, data)
                    }
                });
            }
        }
    });
});
