var TDC = TDC || { suggestions: [] };

(function() {
    var $ = jQuery;

    TDC.instances = TDC.instances || {};

    var BaseView = Backbone.View.extend({

        initialize: function(options) {
            Backbone.View.prototype.initialize.apply(this, arguments);
            this.controller = (options.controller)? options.controller : null;
            return this;
        },

        showSpinner: function() {
            this.$el.find('.spinner').css('display', 'inline-block');
            this.$el.find('.spinner').css('visibility', 'visible');
        },

        hideSpinner: function() {
            this.$el.find('.spinner').css('display', 'none');
            this.$el.find('.spinner').css('visibility', 'hidden');
        }
    });

    var SuggestionForm = BaseView.extend({
        events: {
            'click .submit': 'getSuggestions'
        },

        getSuggestions: function(event) {
            var self = this;

            if (typeof event !=='undefined' &&
                event.type =='click' &&
                typeof self.ongoing !== 'undefined' &&
                $.inArray(self.ongoing.state(), ['resolved', 'rejected']) == -1)
            {
                return false;
            }

            var opts = {
                    url: ajaxurl,
                    data: {
                        action: 'tdc_ajax_get_consolidation_suggestions',
                        security: 'none',
                        request: JSON.stringify({
                            taxonomy: 'post_tag',
                            page: self.controller.page
                        })
                    },
                    dataType: 'json',
                    method: 'post',
                    success: function(data) {
                        self.trigger('suggestionsPopulated', data.suggestions);
                        self.hideSpinner();
                    }
                };

            self.showSpinner();
            self.ongoing = $.ajax(opts);
            return false;
        }
    });

    var SuggestionList = BaseView.extend({
        page: 1,

        initialize: function(attributes, options) {
            Backbone.View.prototype.initialize.apply(this, arguments);
            this.suggestion_form = new SuggestionForm({
                el: '#tdc-suggestions-request',
                controller: this
            });
            this.suggestion_form.on('suggestionsPopulated', this.render.bind(this));
            return this;
        },

        render: function(suggestions) {
            var self = this;
                template = _.template($('#tdc-suggestion-tmpl').html());

            self.$el.html('');

            _.each(suggestions, function(group, i) {
                self.$el.append(template({ group: group }));
            });
        }
    });

    $(document).ready(function() {
        TDC.instances.suggestion_list = new SuggestionList({ el: '#tdc-suggestions-list' });
    });

}());
