define(["exports"], function (_exports) {
    "use strict";

    Object.defineProperty(_exports, "__esModule", {
        value: true
    });
    _exports.Storage = _exports.Localize = _exports.Listener$1 = _exports.Listener = _exports.FavoriteService = _exports.EventManagerAware = _exports.EventManager = _exports.Event = _exports.$index = _exports.$Storage = _exports.$Localize = _exports.$Listener = _exports.$FavoriteService = _exports.$EventManagerAware = _exports.$EventManager = _exports.$Event = void 0;

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
                    var _this = this;

                    return new Promise(function (resolve, reject) {
                        _this.adapter.get(id).then(function (data) {
                            // TODO add event
                            resolve(_this.getHydrator() ? _this.getHydrator().hydrate(data) : data);
                        }).catch(function (error) {
                            reject(error);
                        });
                    });
                }
            }, {
                key: "getAll",
                value: function getAll(filter) {
                    var _this2 = this;

                    return new Promise(function (resolve, reject) {
                        _this2.adapter.getAll(filter).then(function (result) {
                            if (_this2.getHydrator()) {
                                for (var cont = 0; result.length > cont; cont++) {
                                    result[cont] = _this2.hydrator ? _this2.hydrator.hydrate(result[cont]) : result[cont];
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
                    var _this3 = this;

                    return new Promise(function (resolve, reject) {
                        _this3.adapter.getPaged(page, itemCount, filter).then(function (result) {
                            if (_this3.getHydrator()) {
                                for (var cont = 0; result.length > cont; cont++) {
                                    result[cont] = _this3.hydrator ? _this3.hydrator.hydrate(result[cont]) : result[cont];
                                }
                            }

                            console.log(_this3.adapter.getNameCollection(), result);
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
                    var _this4 = this;

                    return new Promise(function (resolve, reject) {
                        _this4.getEventManager().emit(Storage.BEFORE_REMOVE, entity);

                        _this4.adapter.remove(entity).then(function (data) {
                            _this4.getEventManager().emit(Storage.POST_REMOVE, entity);

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
                    var _this5 = this;

                    return new Promise(function (resolve, reject) {
                        _this5.getEventManager().emit(Storage.BEFORE_SAVE, entity);

                        var data = _this5.hydrator ? _this5.hydrator.extract(entity) : entity;

                        _this5.adapter.save(data).then(function (data) {
                            entity = _this5.hydrator ? _this5.hydrator.hydrate(data) : entity;

                            _this5.getEventManager().emit(Storage.POST_SAVE, entity);

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
                    var _this6 = this;

                    return new Promise(function (resolve, reject) {
                        _this6.getEventManager().emit(Storage.BEFORE_UPDATE, entity);

                        var data = _this6.hydrator ? _this6.hydrator.extract(entity) : entity;

                        _this6.adapter.update(data).then(function (data) {
                            _this6.getEventManager().emit(Storage.POST_UPDATE, entity);

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

    var FavoriteService =
        /*#__PURE__*/
        function () {
            /**
             * @param {StorageInterface} storage
             * @param {object} menu
             */
            function FavoriteService(storage, menu) {
                var _this7 = this;

                babelHelpers.classCallCheck(this, FavoriteService);

                /**
                 * @type Array
                 */
                this.favorites = [];
                /**
                 * @type string
                 */

                this.identifier = '_id';
                this.storage = storage;
                this.setMenu(menu);
                this.storage.getEventManager().on(Storage.POST_REMOVE, function (data) {
                    var index = _this7.favorites.findIndex(function (element) {
                        return element[_this7.identifier] === data.data[_this7.identifier];
                    });

                    if (index >= 0) {
                        _this7.favorites.splice(index, 1);
                    }
                });
            }
            /**
             * @return {object}
             */


            babelHelpers.createClass(FavoriteService, [{
                key: "getMenu",
                value: function getMenu() {
                    return this.menu;
                }
                /**
                 * @param {object} menu
                 * @return FavoriteService
                 */

            }, {
                key: "setMenu",
                value: function setMenu(menu) {
                    this.menu = menu;

                    this._loadFavorites();

                    return this;
                }
                /**
                 * @private
                 */

            }, {
                key: "_loadFavorites",
                value: function _loadFavorites() {
                    var _this8 = this;

                    this.getFavorites().then(function (favorites) {
                        if (favorites) {
                            _this8.favorites = favorites;
                        }
                    });
                }
                /**
                 * @return EventManagerInterface
                 */

            }, {
                key: "getEventManager",
                value: function getEventManager() {
                    return this.storage.getEventManager();
                }
                /**
                 * @param eventManager
                 */

            }, {
                key: "setEventManager",
                value: function setEventManager(eventManager) {
                    this.storage.setEventManager(eventManager);
                    return this;
                }
                /**
                 * @param {EntityIdentifierInterface} menuItem
                 * @return {FavoriteService}
                 */

            }, {
                key: "addFavorite",
                value: function addFavorite(menuItem) {
                    var _this9 = this;

                    var favorite;
                    var newFavorite = false;

                    if (this.hasFavorite(menuItem)) {
                        favorite = this.getFavorite(menuItem);
                        favorite.totalCount++;
                    } else {
                        favorite = menuItem;
                        favorite.totalCount = 1;
                        favorite.currentCount = 0;
                        favorite.restaurantId = this.getRestaurantId();
                        this.favorites.push(favorite);
                        newFavorite = true;
                    }

                    this.storage.update(favorite).then(function (data) {
                        if (newFavorite) {
                            _this9.getEventManager().emit(FavoriteService.NEW_FAVORITES, data);
                        }
                    });
                    return this;
                }
                /**
                 * @param {EntityIdentifierInterface} menuItem
                 * @return {FavoriteService}
                 */

            }, {
                key: "removeFavorite",
                value: function removeFavorite(menuItem) {
                    if (this.hasFavorite(menuItem)) {
                        var favorite = this.getFavorite(menuItem);

                        if (favorite.totalCount > 0) {
                            favorite.totalCount--;

                            if (favorite.currentCount > favorite.totalCount) {
                                favorite.currentCount = favorite.totalCount;
                            }

                            this.upsertFavorite(favorite);
                        }
                    }

                    return this;
                }
                /**
                 * @param {EntityIdentifierInterface} favorite
                 * @return Promise<any>
                 */

            }, {
                key: "upsertFavorite",
                value: function upsertFavorite(favorite) {
                    return this.storage.update(favorite);
                }
                /**
                 * @param {EntityIdentifierInterface} menuItem
                 */

            }, {
                key: "hasFavorite",
                value: function hasFavorite(menuItem) {
                    var _this10 = this;

                    return this.favorites.findIndex(function (element) {
                        return element[_this10.identifier] === menuItem[_this10.identifier];
                    }) > -1;
                }
                /**
                 * @param menuItem
                 */

            }, {
                key: "getFavorite",
                value: function getFavorite(menuItem) {
                    var _this11 = this;

                    return this.favorites.find(function (element) {
                        return element[_this11.identifier] === menuItem[_this11.identifier];
                    });
                }
                /**
                 * @return Array
                 */

            }, {
                key: "getFavorites",
                value: function getFavorites() {
                    return this.storage.getAll({
                        restaurantId: this.menu["organization"][this.identifier]
                    });
                }
                /**
                 * @param menuItem
                 */

            }, {
                key: "deleteFavorite",
                value: function deleteFavorite(menuItem) {
                    return this.storage.delete(menuItem);
                }
                /**
                 * @return {number}
                 */

            }, {
                key: "getAmount",
                value: function getAmount() {
                    var amount = 0;

                    for (var _index = 0; this.favorites.length > _index; _index++) {
                        amount = amount + this.favorites[_index].price.value * this.favorites[_index].totalCount;
                    }

                    return amount;
                }
                /**
                 * @return {string}
                 */

            }, {
                key: "getRestaurantId",
                value: function getRestaurantId() {
                    if (!this.menu['organization'] || !this.menu['organization'][this.identifier]) {
                        throw new Error('Restaurant id not found');
                    }

                    return this.menu['organization'][this.identifier];
                }
                /**
                 *
                 * @param identifier
                 */

            }, {
                key: "setIdentifier",
                value: function setIdentifier(identifier) {
                    this.identifier = identifier;
                }
                /**
                 *
                 */

            }, {
                key: "resetFavorites",
                value: function resetFavorites() {
                    var _this12 = this;

                    this.getFavorites().then(function (data) {
                        console.log('reset', data);
                        var favorites = [];

                        for (var _index2 = 0; data.length > _index2; _index2++) {
                            data[_index2].currentCount = 0;
                            favorites.push(_this12.upsertFavorite(data[_index2]));
                        }

                        Promise.all(favorites).then(function (data) {
                            _this12.getEventManager().emit(FavoriteService.RESET_FAVORITES, data);
                        });
                    });
                }
            }]);
            return FavoriteService;
        }();
    /**
     * Constants
     */


    _exports.FavoriteService = FavoriteService;
    FavoriteService.RESET_FAVORITES = "reset-favorites";
    /**
     * Constants
     */

    FavoriteService.NEW_FAVORITES = "new-favorites";
    var FavoriteService$1 = {
        FavoriteService: FavoriteService
    };
    _exports.$FavoriteService = FavoriteService$1;

    var Localize =
        /*#__PURE__*/
        function (_EventManagerAware) {
            babelHelpers.inherits(Localize, _EventManagerAware);

            /**
             * @param defaultLang
             * @param languages
             */
            function Localize(defaultLang, languages) {
                var _this13;

                babelHelpers.classCallCheck(this, Localize);
                _this13 = babelHelpers.possibleConstructorReturn(this, babelHelpers.getPrototypeOf(Localize).call(this));
                /**
                 * @type {string[]}
                 */

                _this13.languages = [];
                _this13.defaultLang = defaultLang;
                _this13.languages = languages;
                return _this13;
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