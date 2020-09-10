var NRD = window.NRD || {};

NRD['./views/DrawerView'] = (function () {
    'use strict';

    var $ = NRD['jquery'];
    var Modernizr = NRD['modernizr'];
    var ACTIVE_CLASS_NAME = 'isActive';
    var SITE_CONTAINER_CLASS = '.drawer-site';
    var DRAWER_TRIGGER_CLASS = '.js-drawerToggle';
    var SNEEZEGUARD = '.drawer-sneezeGuard';
    var CLOSE_BTN = '.js-closeBtn';

    /**
     *
     * @class DrawerView
     * @param {jQuery} $element A reference to the containing DOM element.
     * @constructor
     */
    var DrawerView = function ($element) {

        this.$drawer = $element;
        this.$activeDrawer = false;


        /**
         * Tracks whether component is enabled.
         *
         * @default false
         * @property isEnabled
         * @type {bool}
         * @public
         */

        this.isEnabled = false;

        this._init();
    };

    var proto = DrawerView.prototype;

    /**
     * Initializes the UI Component View.
     * Runs a single _setupHandlers call, followed by _createChildren and layout.
     * Exits early if it is already initialized.
     *
     * @method init
     * @returns {DrawerView}
     * @private
     */
    proto._init = function () {
        this._setupHandlers()
            ._createChildren()
            ._addIOSClass()
            .enable();


        return this;
    };

    /**
     * Binds the scope of any handler functions.
     * Should only be run on initialization of the view.
     *
     * @method _setupHandlers
     * @returns {DrawerView}
     * @private
     */
    proto._setupHandlers = function () {
        // Bind event handlers scope here
        this._onClickToggleHandler = $.proxy(this._onClickToggle, this);
        this._onDocumentClickHandler = $.proxy(this._onDocumentToggle, this);
        this._onCloseBtnClickHandler = $.proxy(this._onCloseBtnClick, this);

        return this;
    };

    /**
     * Create any child objects or references to DOM elements.
     * Should only be run on initialization of the view.
     *
     * @method _createChildren
     * @returns {DrawerView}
     * @private
     */
    proto._createChildren = function () {
        this.$drawerSite = this.$drawer.find(SITE_CONTAINER_CLASS);
        this.$drawerTrigger = $(DRAWER_TRIGGER_CLASS);
        //this.$sneezeGuard = this.$drawer.find(SNEEZEGUARD);
        this.$closeBtn = this.$drawer.find(CLOSE_BTN);
        return this;
    };

    /**
     * Remove any child objects or references to DOM elements.
     *
     * @method removeChildren
     * @returns {DrawerView}
     * @public
     */
    proto.removeChildren = function () {

        return this;
    };

    /**
     * Enables the component.
     * Performs any event binding to handlers.
     * Exits early if it is already enabled.
     *
     * @method enable
     * @returns {DrawerView}
     * @public
     */
    proto.enable = function () {
        if (this.isEnabled) {
            return this;
        }
        this.isEnabled = true;

        this.$drawerTrigger.on('click touchstart', this._onClickToggleHandler);
        //this.$sneezeGuard.on('click touchstart', this._onClickToggleHandler);
        this.$closeBtn.on('click touchstart', this._onCloseBtnClickHandler);

        return this;
    };

    /**
     * Disables the component.
     * Tears down any event binding to handlers.
     * Exits early if it is already disabled.
     *
     * @method disable
     * @returns {DrawerView}
     * @public
     */
    proto.disable = function () {
        if (!this.isEnabled) {
            return this;
        }
        this.isEnabled = false;

        this.$drawerTrigger.off('click', this._onClickToggleHandler);
        this._setExpandedRole(this.$drawerTrigger, false);
        this._setExpandedRole(this.$drawer, false);

        return this;
    };

    /**
     * Destroys the component.
     * Tears down any events, handlers, elements.
     * Should be called when the object should be left unused.
     *
     * @method destroy
     * @returns {DrawerView}
     * @public
     */
    proto.destroy = function () {
        this.disable()
            .removeChildren();

        return this;
    };

    /**
     * On Click Menu Handler
     * Toggles visibilty of main navigation menu
     * @param  {Event} e JavaScript event object
     */
    proto._onClickToggle = function (e) {

        e.preventDefault();

        this._toggleDrawer();
    };

    /**
     * Document click function
     *
     * @method onDocumentClick
     * @param  {Object} event The event object returned from the click
     * @return if isActive === true
     */
    proto._onDocumentClick = function () {

        if (this.$activeDrawer === true) {
            this._toggleDrawer();
        } else {
            return;
        }
    };

    /**
     * Close Button click function
     *
     * @method onCloseBtnClick
     * @param  {Object} event The event object returned from the click
     * @return if isActive === true
     */
    proto._onCloseBtnClick = function () {

        if (this.$activeDrawer === true) {
            this._toggleDrawer();
        } else {
            return;
        }
    };

    /**
     * Main menu toggle function
     * controls adding/removing of active class
     * updates aria attributes
     *
     * @method _toggleDrawer
     * @chainable
     */
    proto._toggleDrawer = function () {

        if (this.$activeDrawer === true) {
            this.$drawer.removeClass(ACTIVE_CLASS_NAME);
            this._setExpandedRole(this.$drawerTrigger, false);
            this._setExpandedRole(this.$drawer, false);
            this.$activeDrawer = false;

            return;

        } else {
            this.$drawer.addClass(ACTIVE_CLASS_NAME);
            this._setExpandedRole(this.$drawerTrigger, true);
            this._setExpandedRole(this.$drawer, true);
            this.$activeDrawer = true;
        }

        return this;
    };

    /**
     * Sets attribute and value on specified elements
     *
     * @method _setExpandedRole
     * @param {jQuery} $ariaElement Element with aria attribute
     * @param {string} value        New aria value
     */
    proto._setExpandedRole = function ($ariaElement, value) {

        $ariaElement[0].setAttribute('aria-expanded', value);
    };

    /**
     * Adds class to drawer if device is running IOS
     *
     * @method _addIOSClass
     * @chainable
     */
    proto._addIOSClass = function () {
        if (this.addIOSDetect() === true) {
            this.$drawer.addClass('isIOS');
        }

        return this;
    };


    /**
     * Detects whether device is running IOS
     * if yes -returns true
     *
     * @method addIOSDetect
     * @returns {boolean} true || false
     */
    proto.addIOSDetect = function () {
        //Add Modernizr test
        Modernizr.addTest('isios', function () {
            return navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false;
        });

        if (Modernizr.isios) {
            return true;
        } else {
            return false;
        }
    };


    return DrawerView;

}());