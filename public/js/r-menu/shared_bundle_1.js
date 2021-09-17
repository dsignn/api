define(["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.Storage = _exports.Localize = _exports.Listener$1 = _exports.Listener = _exports.EventManagerAware = _exports.EventManager = _exports.Event = _exports.$index = _exports.$Storage = _exports.$Localize = _exports.$Listener = _exports.$EventManagerAware = _exports.$EventManager = _exports.$Event = void 0;

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
  _exports.$Event = Event$1;

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
              case babelHelpers.typeof(this.listeners[evtName][cont]) === 'object' && typeof this.listeners[evtName][cont]['execute'] == 'function':
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
  /**
   * @class
   * Listener
   */

  _exports.$EventManagerAware = EventManagerAware$1;

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

  var Storage =
  /*#__PURE__*/
  function () {
    /**
     * @param {StorageAdapterInterface} adapter
     */
    function Storage(adapter) {
      babelHelpers.classCallCheck(this, Storage);

      /**
       * @type {EventManagerInterface}
       */
      this.eventManager = new EventManager();
      /**
       * @type {StorageAdapterInterface}
       */

      this.adapter = adapter;
    }
    /**
     * @param {EventManagerInterface} eventManager
     * @return {this}
     */


    babelHelpers.createClass(Storage, [{
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
      /**
       * @return {HydratorInterface}
       */

    }, {
      key: "getHydrator",
      value: function getHydrator() {
        return this.hydrator;
      }
      /**
       * @param {HydratorInterface} hydrator
       */

    }, {
      key: "setHydrator",
      value: function setHydrator(hydrator) {
        this.hydrator = hydrator;
        return this;
      }
      /**
       * @inheritDoc
       */

    }, {
      key: "setAdapter",
      value: function setAdapter(adapter) {
        this.adapter = adapter;
        return this;
      }
      /**
       * @inheritDoc
       */

    }, {
      key: "getAdapter",
      value: function getAdapter() {
        return this.adapter;
      }
      /**
       * @inheritDoc
       */

    }, {
      key: "get",
      value: function get(id) {
        var _this2 = this;

        return new Promise(function (resolve, reject) {
          _this2.adapter.get(id).then(function (data) {
            // TODO add event
            resolve(_this2.getHydrator() ? _this2.getHydrator().hydrate(data) : data);
          }).catch(function (error) {
            reject(error);
          });
        });
      }
    }, {
      key: "getAll",
      value: function getAll(filter) {
        var _this3 = this;

        return new Promise(function (resolve, reject) {
          _this3.adapter.getAll(filter).then(function (result) {
            if (_this3.getHydrator()) {
              for (var cont = 0; result.length > cont; cont++) {
                result[cont] = _this3.hydrator ? _this3.hydrator.hydrate(result[cont]) : result[cont];
              }
            }

            resolve(result);
          }).catch(function (error) {
            reject(error);
          });
        });
      }
    }, {
      key: "getPaged",
      value: function getPaged(page, itemCount, filter) {
        var _this4 = this;

        return new Promise(function (resolve, reject) {
          _this4.adapter.getPaged(page, itemCount, filter).then(function (result) {
            if (_this4.getHydrator()) {
              for (var cont = 0; result.length > cont; cont++) {
                result[cont] = _this4.hydrator ? _this4.hydrator.hydrate(result[cont]) : result[cont];
              }
            }

            console.log(_this4.adapter.getNameCollection(), result);
            resolve(result);
          }).catch(function (error) {
            reject(error);
          });
        });
      }
      /**
       * @inheritDoc
       */

    }, {
      key: "delete",
      value: function _delete(entity) {
        var _this5 = this;

        return new Promise(function (resolve, reject) {
          _this5.getEventManager().emit(Storage.BEFORE_REMOVE, entity);

          _this5.adapter.remove(entity).then(function (data) {
            _this5.getEventManager().emit(Storage.POST_REMOVE, entity);

            resolve(entity);
          }).catch(function (error) {
            reject(error);
          });
        });
      }
      /**
       * @inheritDoc
       */

    }, {
      key: "save",
      value: function save(entity) {
        var _this6 = this;

        return new Promise(function (resolve, reject) {
          _this6.getEventManager().emit(Storage.BEFORE_SAVE, entity);

          var data = _this6.hydrator ? _this6.hydrator.extract(entity) : entity;

          _this6.adapter.save(data).then(function (data) {
            entity = _this6.hydrator ? _this6.hydrator.hydrate(data) : entity;

            _this6.getEventManager().emit(Storage.POST_SAVE, entity);

            resolve(entity);
          }).catch(function (err) {
            reject(err);
          });
        });
      }
      /**
       * @inheritDoc
       */

    }, {
      key: "update",
      value: function update(entity) {
        var _this7 = this;

        return new Promise(function (resolve, reject) {
          _this7.getEventManager().emit(Storage.BEFORE_UPDATE, entity);

          var data = _this7.hydrator ? _this7.hydrator.extract(entity) : entity;

          _this7.adapter.update(data).then(function (data) {
            _this7.getEventManager().emit(Storage.POST_UPDATE, entity);

            resolve(entity);
          }).catch(function (err) {
            reject(err);
          });
        });
      }
    }]);
    return Storage;
  }();
  /**
   * Constants
   */


  _exports.Storage = Storage;
  Storage.BEFORE_SAVE = "after-save";
  Storage.POST_SAVE = "post-save";
  Storage.BEFORE_UPDATE = "after-update";
  Storage.POST_UPDATE = "post-update";
  Storage.BEFORE_REMOVE = "after-remove";
  Storage.POST_REMOVE = "post-remove";
  Storage.BEFORE_GET = "after-get";
  Storage.POST_GET = "post-get";
  var Storage$1 = {
    Storage: Storage
  };
  _exports.$Storage = Storage$1;
});