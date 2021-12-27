define(["exports"], function (_exports) {
  "use strict";

  Object.defineProperty(_exports, "__esModule", {
    value: true
  });
  _exports.Storage = _exports.OrderService = _exports.OrderItemWrapper = _exports.OrderEntity = _exports.Localize = _exports.Listener$1 = _exports.Listener = _exports.EventManagerAware = _exports.EventManager = _exports.Event = _exports.EntityIdentifier = _exports.$index = _exports.$Storage = _exports.$OrderService = _exports.$OrderItemWrapper = _exports.$OrderEntity = _exports.$Localize = _exports.$Listener = _exports.$EventManagerAware = _exports.$EventManager = _exports.$Event = _exports.$EntityIdentifier = void 0;

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
            } //console.log(this.adapter.getNameCollection(), result);


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
  /**
   *
   */

  _exports.$Storage = Storage$1;

  var EntityIdentifier =
  /*#__PURE__*/
  function () {
    function EntityIdentifier() {
      babelHelpers.classCallCheck(this, EntityIdentifier);
    }

    babelHelpers.createClass(EntityIdentifier, [{
      key: "getId",

      /**
       * @inheritDoc
       */
      value: function getId() {
        return this.id;
      }
      /**
       * @inheritDoc
       */

    }, {
      key: "setId",
      value: function setId(id) {
        this.id = id;
        return this;
      }
    }]);
    return EntityIdentifier;
  }();

  _exports.EntityIdentifier = EntityIdentifier;
  var EntityIdentifier$1 = {
    EntityIdentifier: EntityIdentifier
  };
  /**
   * @class OrderEntity
   */

  _exports.$EntityIdentifier = EntityIdentifier$1;

  var OrderItemWrapper =
  /*#__PURE__*/
  function () {
    babelHelpers.createClass(OrderItemWrapper, null, [{
      key: "STATUS_TO_DO",

      /**
       * Dish to do
       *
       * @return {string}
       */
      get: function get() {
        return 'to_do';
      }
      /**
       * Dish able to be delivering
       *
       * @return {string}
       */

    }, {
      key: "STATUS_DELIVERED",
      get: function get() {
        return 'delivered';
      }
      /**
       * Dish terminate
       *
       * @return {string}
       */

    }, {
      key: "STATUS_TERMINATE",
      get: function get() {
        return 'terminate';
      }
    }]);

    function OrderItemWrapper(item) {
      babelHelpers.classCallCheck(this, OrderItemWrapper);

      /**
       * 
       */
      this.ordered = item;
      this.status = OrderItemWrapper.STATUS_TO_DO;
    }

    return OrderItemWrapper;
  }();

  _exports.OrderItemWrapper = OrderItemWrapper;
  var OrderItemWrapper$1 = {
    OrderItemWrapper: OrderItemWrapper
  };
  _exports.$OrderItemWrapper = OrderItemWrapper$1;

  var OrderEntity =
  /*#__PURE__*/
  function (_EntityIdentifier) {
    babelHelpers.inherits(OrderEntity, _EntityIdentifier);
    babelHelpers.createClass(OrderEntity, null, [{
      key: "STATUS_CHECK",

      /**
       * Status to check
       *
       * @return {string}
       */
      get: function get() {
        return 'check';
      }
      /**
       * Status on queue
       *
       * @return {string}
       */

    }, {
      key: "STATUS_QUEUE",
      get: function get() {
        return 'queue';
      }
      /**
       * Status on preparation
       *
       * @return {string}
       */

    }, {
      key: "STATUS_PREPARATION",
      get: function get() {
        return 'preparation';
      }
      /**
       * Status at table
       *
       * @return {string}
       */

    }, {
      key: "STATUS_DELIVERING",
      get: function get() {
        return 'delivering';
      }
      /**
       * Status at table
       *
       * @return {string}
       */

    }, {
      key: "STATUS_CLOSE",
      get: function get() {
        return 'close';
      }
      /**
       * Status at table
       *
       * @return {string}
       */

    }, {
      key: "STATUS_INVALID",
      get: function get() {
        return 'invalid';
      }
      /**
       * State of order
       */

    }, {
      key: "FINITE_STATE_MACHINE",
      get: function get() {
        var variable = {};
        variable[OrderEntity.STATUS_CHECK] = [OrderEntity.STATUS_QUEUE, OrderEntity.STATUS_INVALID];
        variable[OrderEntity.STATUS_QUEUE] = [OrderEntity.STATUS_PREPARATION];
        variable[OrderEntity.STATUS_PREPARATION] = [OrderEntity.STATUS_QUEUE, OrderEntity.STATUS_DELIVERING, OrderEntity.STATUS_CLOSE];
        variable[OrderEntity.STATUS_DELIVERING] = [OrderEntity.STATUS_CLOSE];
        return variable;
      }
    }]);

    function OrderEntity() {
      var _this8;

      babelHelpers.classCallCheck(this, OrderEntity);
      _this8 = babelHelpers.possibleConstructorReturn(this, babelHelpers.getPrototypeOf(OrderEntity).call(this));
      _this8.id = null;
      _this8.name = null;
      _this8.additionalInfo = {};
      /**
       * @type {Array}
       */

      _this8.items = [];
      _this8.status = OrderEntity.STATUS_CHECK;
      _this8.createdAt = null;
      _this8.lastUpdateAt = null;
      _this8.organization = {};
      _this8.currenteSelected = false;
      return _this8;
    }
    /**
     * @param {string} key 
     * @param {any} value
     * @returns 
     */


    babelHelpers.createClass(OrderEntity, [{
      key: "pushAdditionInfo",
      value: function pushAdditionInfo(key, value) {
        this.additionalInfo[key] = value;
        return this;
      }
      /**
       * 
       * @param {string} id 
       * @param {string} ?status 
       * @returns 
       */

    }, {
      key: "getTotalItemOrder",
      value: function getTotalItemOrder(id, status) {
        var total = 0;

        for (var cont = 0; this.items.length > cont; cont++) {
          if (id === this.items[cont].ordered._id && (status === undefined || status !== undefined && status === this.items[cont].status)) {
            total++;
          }

          if (id === undefined && (status === undefined || status !== undefined && status === this.items[cont].status)) {
            total++;
          }
        }

        return total;
      }
      /**
       * @param {string} id 
       */

    }, {
      key: "getItemOrderPrice",
      value: function getItemOrderPrice(id) {
        var price = {
          value: 0
        };

        var itemOrder = this._getItemOrder(id);

        if (!itemOrder || !itemOrder.ordered || !itemOrder.ordered.price) {
          return price;
        }

        var total = this.getTotalItemOrder(id);
        price.value = itemOrder.ordered.price.value * total; // TODO PRICE OBJECT

        return price;
      }
      /**
       * @param {string} id 
       */

    }, {
      key: "getTotalItemOrderPrice",
      value: function getTotalItemOrderPrice() {
        var orderItems = this.getDistinctItemOrder();
        var price = {
          value: 0
        };
        var tmpPrice;

        for (var cont = 0; orderItems.length > cont; cont++) {
          tmpPrice = this.getItemOrderPrice(orderItems[cont].ordered._id);
          price.value += tmpPrice.value;
        }

        return price;
      }
      /**
       * @param {string} id 
       * @returns 
       */

    }, {
      key: "_getItemOrder",
      value: function _getItemOrder(id) {
        return this.items.find(function (element) {
          return element.ordered._id === id;
        });
      }
      /**
       * @param {object} item 
       * @returns OrderEntity
       */

    }, {
      key: "addItemOrder",
      value: function addItemOrder(item) {
        this.items.push(new OrderItemWrapper(item));
        return this;
      }
      /**
       * @param {object} item 
       * @returns OrderEntity
       */

    }, {
      key: "removeItemOrder",
      value: function removeItemOrder(item) {
        var index = this.items.findIndex(function (element) {
          // TODO get id
          return element.ordered._id === item._id && element.status === OrderItemWrapper.STATUS_TO_DO;
          ;
        });

        if (index > -1) {
          this.items.splice(index, 1);
        }

        return this;
      }
      /**
       * @returns Array
       */

    }, {
      key: "getDistinctItemOrder",
      value: function getDistinctItemOrder() {
        var _this9 = this;

        var orders = [];

        var _loop = function _loop(cont) {
          var has = orders.find(function (element) {
            /**
             * TODO add getId()
             */
            return element.ordered._id === _this9.items[cont].ordered._id;
          });

          if (!has) {
            orders.push(_this9.items[cont]);
          }
        };

        for (var cont = 0; this.items.length > cont; cont++) {
          _loop(cont);
        }

        return orders;
      }
    }]);
    return OrderEntity;
  }(EntityIdentifier);

  _exports.OrderEntity = OrderEntity;
  var OrderEntity$1 = {
    OrderEntity: OrderEntity
  };
  _exports.$OrderEntity = OrderEntity$1;

  var OrderService =
  /*#__PURE__*/
  function (_EventManagerAware2) {
    babelHelpers.inherits(OrderService, _EventManagerAware2);
    babelHelpers.createClass(OrderService, null, [{
      key: "CHANGE_DEFAUL_ORDER",

      /**
       * Name of the "message" send from sender when play timeslot
       *
       * @return {string}
       */
      get: function get() {
        return 'change-default-order';
      }
      /**
       * Name of the "message" send from sender when play timeslot
       *
       * @return {string}
       */

    }, {
      key: "LOAD_DEFAUL_ORDER",
      get: function get() {
        return 'load-default-order';
      }
      /**
       * 
       * @param {StorageInterface} storage 
       */

    }]);

    function OrderService(storage) {
      var _this10;

      babelHelpers.classCallCheck(this, OrderService);
      _this10 = babelHelpers.possibleConstructorReturn(this, babelHelpers.getPrototypeOf(OrderService).call(this));
      _this10.storage = storage;
      _this10.currentOrder = null;
      return _this10;
    }

    babelHelpers.createClass(OrderService, [{
      key: "getStorage",
      value: function getStorage() {
        return this.storage;
      }
      /** 
       *  @returns OrderEntity|null
       */

    }, {
      key: "getCurrentOrder",
      value: function getCurrentOrder() {
        return this.currentOrder;
      }
      /**
       * 
       * @param {OrderEntity} currentOrder 
       * @returns OrderService
       */

    }, {
      key: "setCurrentOrder",
      value: function () {
        var _setCurrentOrder = babelHelpers.asyncToGenerator(
        /*#__PURE__*/
        regeneratorRuntime.mark(function _callee(order) {
          var allOrder, cont;
          return regeneratorRuntime.wrap(function _callee$(_context) {
            while (1) {
              switch (_context.prev = _context.next) {
                case 0:
                  _context.next = 2;
                  return this.getStorage().getAll({
                    'restaurantId': order.organization.id
                  });

                case 2:
                  allOrder = _context.sent;
                  cont = 0;

                case 4:
                  if (!(allOrder.length > cont)) {
                    _context.next = 11;
                    break;
                  }

                  allOrder[cont].currenteSelected = false;
                  _context.next = 8;
                  return this.getStorage().update(allOrder[cont]);

                case 8:
                  cont++;
                  _context.next = 4;
                  break;

                case 11:
                  order.currenteSelected = true;
                  this.currentOrder = order;
                  _context.next = 15;
                  return this.getStorage().update(order);

                case 15:
                  this.getEventManager().emit(OrderService.CHANGE_DEFAUL_ORDER, order);
                  return _context.abrupt("return", this);

                case 17:
                case "end":
                  return _context.stop();
              }
            }
          }, _callee, this);
        }));

        function setCurrentOrder(_x) {
          return _setCurrentOrder.apply(this, arguments);
        }

        return setCurrentOrder;
      }()
      /**
       * @param {string} restaurantId 
       * @returns 
       */

    }, {
      key: "loadCurreOrder",
      value: function () {
        var _loadCurreOrder = babelHelpers.asyncToGenerator(
        /*#__PURE__*/
        regeneratorRuntime.mark(function _callee2(restaurantId) {
          var orders, order;
          return regeneratorRuntime.wrap(function _callee2$(_context2) {
            while (1) {
              switch (_context2.prev = _context2.next) {
                case 0:
                  _context2.next = 2;
                  return this.getStorage().getAll({
                    'restaurantId': restaurantId,
                    'currenteSelected': true
                  });

                case 2:
                  orders = _context2.sent;

                  if (!(orders > 1)) {
                    _context2.next = 6;
                    break;
                  }

                  console.warn('too many orders set as default', restaurantId);
                  return _context2.abrupt("return");

                case 6:
                  order = null;

                  if (orders.length > 0) {
                    this.currentOrder = orders[0];
                    this.getEventManager().emit(OrderService.LOAD_DEFAUL_ORDER, this.currentOrder);
                  }

                  return _context2.abrupt("return", this.currentOrder);

                case 9:
                case "end":
                  return _context2.stop();
              }
            }
          }, _callee2, this);
        }));

        function loadCurreOrder(_x2) {
          return _loadCurreOrder.apply(this, arguments);
        }

        return loadCurreOrder;
      }()
    }]);
    return OrderService;
  }(EventManagerAware);

  _exports.OrderService = OrderService;
  var OrderService$1 = {
    OrderService: OrderService
  };
  _exports.$OrderService = OrderService$1;
});