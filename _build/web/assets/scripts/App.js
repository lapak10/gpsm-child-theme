var NRD = window.NRD || {};

NRD['./App'] = (function () {
    'use strict';

    // var Modernizr = NRD['modernizr'];
    var $ = NRD['jquery'];
    var DrawerView = NRD['./views/DrawerView'];
    // var ROIView = NRD['./views/ROIView'];
    // var FunnelView = NRD['./views/FunnelView'];
    // var MegaMenuView = NRD['./views/MegaMenuView'];
    // var MagnificPopupView = NRD['./views/MagnificPopupView'];

    /**
     * Initial application setup. Runs once upon every page load.
     *
     * @class App
     * @constructor
     */
    var App = function () {
        this.init();
    };

    var proto = App.prototype;

    /**
     * Initializes the application and kicks off loading of prerequisites.
     *
     * @method init
     * @private
     */
    proto.init = function () {
        // Create your views here

        this.DrawerView = new DrawerView($('.js-drawer'));
        /**
    this.MagnificPopupView = new MagnificPopupView();
    */
        // this.ROIView = new ROIView($('.js-roiCalc'));
        // this.FunnelView = new FunnelView($('.js-funnel'));
        // this.MegaMenuView = new MegaMenuView($('.mega-menu'));
    };

    return App;

}());