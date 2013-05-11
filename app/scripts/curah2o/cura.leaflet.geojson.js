(function(window, document, undefined) {

	var waterIcon = L.divIcon({
		iconSize: false, // use css defined size
		className: 'icon-tint',
		iconAnchor: [5, 2]
	});

	var lastLayer;
	var locations;
	var allLayers = [];

	Cura.GeoJSON = L.GeoJSON.extend({
		options: {
			pointToLayer: function(featureData, latlng) {
				var marker = L.marker(latlng, {
					riseOnHover: true,
					icon: waterIcon
				});
				return marker;
			},
			// this 'layer' param is above 'marker' when geometry type is Point
			onEachFeature: function(feature, layer) {
				var p = feature.properties;
				var c = feature.geometry.coordinates;
				layer.bindPopup(['<strong>' + p.station_name + '(' + p.location_id + ')</strong>',
					'<br />[ ' + c[0] + ', ' + c[1] + ' ]',
					'<br />' + p.watershed_name].join(''));
				layer.on('click', this.highlightIcon);
				layer.on('click', this.onFeatureClick); // need layer to be the future 'this'
				allLayers.push(layer);
			},
			highlightIcon: function() {
				if (lastLayer) {
					lastLayer._icon.className = lastLayer._icon.className.replace(' highlighted', '');
					lastLayer._resetZIndex();
					L.DomEvent.on(lastLayer._icon, 'mouseover', lastLayer._bringToFront, lastLayer)
					L.DomEvent.on(lastLayer._icon, 'mouseout', lastLayer._resetZIndex, lastLayer);
				}

				this._icon.className = this._icon.className + ' highlighted';
				this._bringToFront();
				L.DomEvent.off(this._icon, 'mouseover', this._bringToFront)
				L.DomEvent.off(this._icon, 'mouseout', this._resetZIndex);

				lastLayer = this;
			},
			onFeatureClick: function() {}
		},

		locations: function() {
			if (locations) {
				return locations;
			}

			var map = {};
			this.eachLayer(function(layer) {
				var key = layer.feature.properties.watershed_name;
				map[key] ? map[key]++ : (map[key] = 1)
			});

			locations = [{
				id: "",
				watershed_name: " - Select community group - "
			}, {
				id: 0,
				watershed_name: "View All"
			}];

			var i = 1;
			for (var key in map) {
				locations.push({
					id: i++,
					watershed_name: key,
					total: map[key]
				})
			}

			return locations;
		},

		filterByCommunityGroup: function(groupId) {
			if (groupId === "") {
				this.clearLayers();
			} else if (groupId === 0) {
				allLayers.forEach(function(layer) {
					this.addLayer(layer);
				}, this);
			} else {
				var watershed_name = "";
				this.locations().every(function(value) {
					if (value.id == id) {
						watershed_name = value.watershed_name;
						return false;
					}
					return true;
				});

				allLayers.forEach(function(layer) {
					if (layer.feature.properties.watershed_name == watershed_name) {
						this.addLayer(layer);
					} else {
						this.removeLayer(layer);
					}
				}, this);
			}
		},

		filterByProperties: function(propValue, propName) {

		}
	});

	Cura.geoJson = function(geojson, options) {
		return new Cura.GeoJSON(geojson, options);
	};
}(this, document));