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
                        self.trigger('suggestionsPopulated', data);
                        self.hideSpinner();
                    }
                };

            if (self.controller.pagination)
                self.controller.pagination.showSpinner();

            self.showSpinner();
            self.ongoing = $.ajax(opts);
            return false;
        }
    });

    var SuggestionList = BaseView.extend({
        page: 1,

        data: {},

        initialize: function(attributes, options) {
            Backbone.View.prototype.initialize.apply(this, arguments);

            this.suggestion_form = new SuggestionForm({
                el: '#tdc-suggestions-request',
                controller: this
            });

            this.suggestion_form.on('suggestionsPopulated', this.render.bind(this));

            return this;
        },

        render: function(data) {
            var self = this;
                template = _.template($('#tdc-suggestion-tmpl').html());

            self.$el.html('');

            _.each(data.suggestions.groups, function(group, i) {
                self.$el.append(template({ group: group }));
            });

            if (!this.pagination) {
                this.pagination = new SuggestionsPagination({
                    el: '#tdc-pagination-container',
                    controller: this
                });
            }
            this.data = data;
            this.pagination.render();
            return false;
        }
    });

    var SuggestionsPagination = BaseView.extend({
        events: {
            'click a.next': 'next',
            'click a.prev': 'prev'
        },

        render: function () {
            this.hideSpinner();

            var attrs = (typeof this.controller.data.suggestions == 'undefined')? {} : this.controller.data.suggestions;

            this.$el.html('');
            this.$el.append(
                _.template($('#tdc-pagination-tmpl').html(), {})
            );

            if (typeof attrs.totalPages == 'undefined')
                return this;

            if (attrs.page <= 1)
                this.$el.find('.prev').addClass('disabled');
            else
                this.$el.find('.prev').removeClass('disabled');

            if (attrs.totalPages > 1)
                this.$el.find('.next').removeClass('disabled');

            if (attrs.page >= attrs.totalPages)
                this.$el.find('.next').addClass('disabled');

            this.updateCount();
        },

        next: function() {
            this.controller.page = this.controller.page + 1;
            this.controller.suggestion_form.getSuggestions();
            return false;
        },

        prev: function() {
            this.controller.page = this.controller.page - 1;
            this.controller.suggestion_form.getSuggestions();
            return false;
        },

        updateCount: function() {
            var attrs = (typeof this.controller.data.suggestions == 'undefined')? {} : this.controller.data.suggestions;

            this.$el.find('.tdc-page').html(attrs.page);
            this.$el.find('.tdc-total-pages').html(attrs.totalPages);
        }
    });

    $(document).ready(function() {
        TDC.instances.suggestion_list = new SuggestionList({ el: '#tdc-suggestions-list' });
    });

}());
