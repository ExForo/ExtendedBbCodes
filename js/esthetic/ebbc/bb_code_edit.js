!function (_0xda11x1, _0xda11x2, _0xda11x3, _0xda11x4) {
	XenForo["BbCodeWysiwygEditor"]["prototype"]["_estebbc_rewrite_getButtonConfig"] = XenForo["BbCodeWysiwygEditor"]["prototype"]["getButtonConfig"];
	XenForo["BbCodeWysiwygEditor"]["prototype"]["getButtonConfig"] = function () {
		var _0xda11x5 = this._estebbc_rewrite_getButtonConfig();
		try {
			var _0xda11x6 = [], _0xda11x7 = {};
			for (j in _0xda11x5["buttons"]) {
				var _0xda11x8 = _0xda11x5["buttons"][j], _0xda11x9 = false;
				if (typeof _0xda11x8 == "object") {
					for (i in _0xda11x8) {
						if (_0xda11x8[i].toString()["match"](/^custom_(club|days|groups|hide|likes|trophies|posts|users|userids|shoppings|organized)$/)) {
							_0xda11x9 = true;
							_0xda11x5["buttonsCustom"][_0xda11x8[i].toString()]["title"] = this["getText"]("estebbc_hide_group_title_" + _0xda11x8[i].toString()["replace"](/^custom_/, ""));
							_0xda11x5["buttonsCustom"][_0xda11x8[i].toString()]["className"] = "icon estebbc_button_" + _0xda11x8[i].toString()["replace"](/^custom_/, "");
							_0xda11x7[_0xda11x8[i].toString()] = _0xda11x5["buttonsCustom"][_0xda11x8[i].toString()];
						}
					}
				}
				if (!_0xda11x9) {
					_0xda11x6["push"](_0xda11x8);
				} else {
					_0xda11x6["push"](["estebbc_hide_group"]);
					_0xda11x5["buttonsCustom"]["estebbc_hide_group"] = {
						title    : this["getText"]("estebbc_hide_group"),
						className: "icon estebbc_group_hidden",
						func     : "show",
						dropdown : _0xda11x7
					};
				}
			}
			return {buttons: _0xda11x6, buttonsCustom: _0xda11x5["buttonsCustom"]};
		} catch (e) {
		}
		return _0xda11x5;
	};
}(jQuery, this, document);