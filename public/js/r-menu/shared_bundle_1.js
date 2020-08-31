define(["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.Localize = _exports.Listener$1 = _exports.Listener = _exports.EventManagerAware = _exports.EventManager = _exports.Event = _exports.$index = _exports.$Localize = _exports.$Listener = _exports.$EventManagerAware = _exports.$EventManager = _exports.$Event = void 0;

  /**
   * @class
   * Event
   */
  var Event =
  /*#__PURE__*/
  function () {
    /**
     * @param {string} name
     * @param {object} data
     */
    function Event(name, data) {
      babelHelpers.classCallCheck(this, Event);

      /**
       * @type {object}
       */
      this.data = {};
      /**
       * @type {boolean}
       */

      this.stopPropagation = false;
      this.name = name;
      this.data = data;
    }
    /**
     * @param {boolean} stopPropagation
     */


    babelHelpers.createClass(Event, [{
      key: "setStopPropagation",
      value: function setStopPropagation(stopPropagation) {
        this.stopPropagation = stopPropagation;
        return this;
      }
      /**
       * @return {boolean}
       */

    }, {
      key: "getStopPropagation",
      value: function getStopPropagation() {
        return this.stopPropagation;
      }
    }]);
    return Event;
  }();

  _exports.Event = Event;
  var Event$1 = {
    Event: Event
  };
  /**
   * @class
   * Listener
   */

  _exports.$Event = Event$1;

  var Listener =
  /*#__PURE__*/
  function () {
    /**
     * @param fn
     */
    function Listener(fn) {
      babelHelpers.classCallCheck(this, Listener);

      if (typeof fn !== 'function') {
        throw "Wrong fn param, must be a function given ".concat(babelHelpers.typeof(fn));
      }
      /**
       * @type {Function}
       */


      this.fn = fn;
    }
    /**
     * @param {Event} event
     * @return {Event}
     */


    babelHelpers.createClass(Listener, [{
      key: "execute",
      value: function execute(event) {
        this.fn(event);
        return event;
      }
    }]);
    return Listener;
  }();

  _exports.Listener$1 = _exports.Listener = Listener;
  var Listener$1 = {
    Listener: Listener
  };
  _exports.$Listener = Listener$1;

  var EventManager =
  /*#__PURE__*/
  function () {
    function EventManager() {
      babelHelpers.classCallCheck(this, EventManager);

      /**
       * @type {object}
       */
      this.listeners = {};
    }
    /**
     * @param {string} evtName
     * @param listener
     * @return EventManager
     */


    babelHelpers.createClass(EventManager, [{
      key: "on",
      value: function on(evtName, listener) {
        if (!this.listeners[evtName]) {
          this.listeners[evtName] = [];
        }

        this.listeners[evtName].push(listener);
        return this;
      }
      /**
       * @param {string} evtName
       * @param {object} params
       * @param {boolean} clearListener
       */

    }, {
      key: "emit",
      value: function emit(evtName, params) {
        var clearListener = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

        if (this.listeners[evtName] !== undefined) {
          var event = new Event(evtName, params);

          for (var cont = 0; this.listeners[evtName].length > cont; cont++) {
            switch (true) {
              case babelHelpers.instanceof(this.listeners[evtName][cont], Listener) === true:
                this.listeners[evtName][cont].execute(event);
                break;

              default:
                this.listeners[evtName][cont](event);
            }

            if (event.getStopPropagation() === true) {
              break;
            }
          }

          if (clearListener) {
            delete this.listeners[evtName];
          }
        }
      }
      /**
       *
       * @param {string} evtName
       * @param {ListenerInterface} listener
       * @return {EventManager}
       */

    }, {
      key: "remove",
      value: function remove(evtName, listener) {
        if (this.listeners[evtName] !== undefined) {
          for (var cont = 0; this.listeners[evtName].length > cont; cont++) {
            if (listener === this.listeners[evtName][cont]) {
              this.listeners[evtName].splice(cont, 1);
              break;
            }
          }
        }

        return this;
      }
    }]);
    return EventManager;
  }();

  _exports.EventManager = EventManager;
  var EventManager$1 = {
    EventManager: EventManager
  };
  _exports.$EventManager = EventManager$1;

  var EventManagerAware =
  /*#__PURE__*/
  function () {
    function EventManagerAware() {
      babelHelpers.classCallCheck(this, EventManagerAware);

      /**
       * @type {EventManager}
       */
      this.eventManager = new EventManager();
    }
    /**
     * @param {EventManagerInterface} eventManager
     * @return {this}
     */


    babelHelpers.createClass(EventManagerAware, [{
      key: "setEventManager",
      value: function setEventManager(eventManager) {
        this.eventManager = eventManager;
        return this;
      }
      /**
       * @return {EventManagerInterface}
       */

    }, {
      key: "getEventManager",
      value: function getEventManager() {
        return this.eventManager;
      }
    }]);
    return EventManagerAware;
  }();

  _exports.EventManagerAware = EventManagerAware;
  var EventManagerAware$1 = {
    EventManagerAware: EventManagerAware
  };
  _exports.$EventManagerAware = EventManagerAware$1;
  var index = {
    Event: Event,
    EventManager: EventManager,
    EventManagerAware: EventManagerAware,
    Listener: Listener
  };
  _exports.$index = index;

  var Localize =
  /*#__PURE__*/
  function (_EventManagerAware) {
    babelHelpers.inherits(Localize, _EventManagerAware);

    /**
     * @param defaultLang
     * @param languages
     */
    function Localize(defaultLang, languages) {
      var _this;

      babelHelpers.classCallCheck(this, Localize);
      _this = babelHelpers.possibleConstructorReturn(this, babelHelpers.getPrototypeOf(Localize).call(this));
      /**
       * @type {string[]}
       */

      _this.languages = [];
      _this.defaultLang = defaultLang;
      _this.languages = languages;
      return _this;
    }
    /**
     * @param {string} language
     * @return {this}
     */


    babelHelpers.createClass(Localize, [{
      key: "setDefaultLang",
      value: function setDefaultLang(language) {
        if (!this.languages.includes(language)) {
          throw 'Language not found';
        }

        if (language === this.defaultLang) {
          return;
        }

        this.defaultLang = language;
        this.getEventManager().emit(Localize.CHANGE_LANGUAGE, {
          'language': this.defaultLang
        });
        return this;
      }
      /**
       * @return {string}
       */

    }, {
      key: "getDefaultLang",
      value: function getDefaultLang() {
        return this.defaultLang;
      }
      /**
       * @return {Array<string>}
       */

    }, {
      key: "getLanguages",
      value: function getLanguages() {
        return this.languages;
      }
    }]);
    return Localize;
  }(EventManagerAware);

  _exports.Localize = Localize;
  Localize.CHANGE_LANGUAGE = 'change-language';
  var Localize$1 = {
    Localize: Localize
  };
  _exports.$Localize = Localize$1;
});