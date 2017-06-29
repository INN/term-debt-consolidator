/**
 * Term Debt Consolidator
 * https://labs.inn.org
 *
 * Licensed under the GPLv2+ license.
 */

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

  var TaxonomySelector = BaseView.extend({
    events: {
      'change input': 'change',
    },

    initialize: function(options) {
      this.suggestion_list = options.suggestion_list;
      BaseView.prototype.initialize.apply(this, arguments);
      this.suggestion_list.suggestion_form.on('suggestionsPopulated', this.enable.bind(this));
      return this;
    },

    disable: function() {
      this.$el.find('input').attr('disabled', 'disabled');
    },

    enable: function() {
      this.$el.find('input').removeAttr('disabled');
    },

    change: function(event) {
      var target = $(event.currentTarget);
      TDC.taxonomy = target.val();
      this.trigger('taxonomy_changed');
      return false;
    }
  });

  var GenerateSuggestions = BaseView.extend({
    events: {
      'click a.tdc-generate-suggestions': 'generateSuggestions'
    },

    page: 1,

    initialize: function() {
      BaseView.prototype.initialize.apply(this, arguments);
      TDC.instances.suggestion_list.suggestion_form.on(
        'suggestionsPopulated', this.suggestionsPopulated.bind(this));
      return this;
    },

    generateSuggestions: function(event) {
      var self = this;

      if (typeof event !== 'undefined' && $(event.currentTarget).hasClass('disabled'))
        return false;

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
            action: 'tdc_ajax_generate_consolidation_suggestions',
            security: TDC.ajax_nonce,
            request: JSON.stringify({
              taxonomy: TDC.taxonomy,
              page: self.page
            })
          },
          dataType: 'json',
          method: 'post',
          success: function(data) {
            if (data.suggestions.page == data.suggestions.totalPages) {
              self.updateProgress(100);
              self.hideProgress();
              self.page = 1;
              self.fetchSuggestions();
            } else {
              self.updateProgress((data.suggestions.page / data.suggestions.totalPages) * 100);
              self.page += 1;
              self.generateSuggestions();
            }
          }
        };

        self.clearSuggestionList();
        self.disableButton();
        self.showProgress();
        self.showSpinner();
        self.ongoing = $.ajax(opts);
        return false;
    },

    updateProgress: function(value) {
      this.$el.find('.tdc-generate-suggestions-progress').progressbar({ value: value });
    },

    showProgress: function() {
      this.$el.find('.tdc-generate-suggestions-progress').progressbar().show();
    },

    hideProgress: function() {
      this.$el.find('.tdc-generate-suggestions-progress').progressbar('destroy').hide();
    },

    disableButton: function() {
      this.$el.find('.tdc-generate-suggestions').addClass('disabled');
    },

    enableButton: function() {
      this.$el.find('.tdc-generate-suggestions').removeClass('disabled');
    },

    clearSuggestionList: function() {
      if (typeof TDC.instances.suggestion_list !== 'undefined') {
        var suggestion_list = TDC.instances.suggestion_list;
        suggestion_list.$el.html('');
        if (suggestion_list.pagination) {
          suggestion_list.pagination.hide();
        }
      }
    },

    fetchSuggestions: function() {
      TDC.instances.suggestion_list.suggestion_form.getSuggestions();
    },

    suggestionsPopulated: function() {
      this.hideSpinner();
      this.enableButton();
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
            security: TDC.ajax_nonce,
            request: JSON.stringify({
              taxonomy: TDC.taxonomy,
              page: self.controller.page
            })
          },
          dataType: 'json',
          method: 'post',
          success: function(data) {
            self.trigger('suggestionsPopulated', data);
            self.hideSpinner();
            self.hideFetching();
          }
        };

        if (self.controller.pagination)
          self.controller.pagination.showSpinner();

        self.showFetching();
        self.showSpinner();
        self.ongoing = $.ajax(opts);
        return false;
    },

    showFetching: function () {
      this.$el.find('.fetching').show();
    },

    hideFetching: function() {
      this.$el.find('.fetching').hide();
    }
  });

  var SuggestionList = BaseView.extend({
    page: 1,

    data: {},

    initialize: function(attributes, options) {
      Backbone.View.prototype.initialize.apply(this, arguments);

      this.suggestion_form = new SuggestionForm({
        el: '#tdc-fetching-suggestions',
        controller: this
      });

      this.suggestion_form.on('suggestionsPopulated', this.render.bind(this));

      return this;
    },

    render: function(data) {
      var self = this,
      template;

      self.$el.html('');

      _.each(data.suggestions.groups, function(group, i) {
        var templateId = (group.length <= 1)? '#tdc-no-suggestion-tmpl' : '#tdc-suggestion-tmpl',
        suggestion = new SuggestionView({
          template: _.template($(templateId).html()),
          group: group,
          controller: self
        });

        suggestion.render();
        self.$el.append(suggestion.$el);
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
        _.template($('#tdc-pagination-tmpl').html())({})
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
      this.show();
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
    },

    hide: function() {
      this.$el.hide();
    },

    show: function() {
      this.$el.show();
    }
  });

  var SuggestionView = BaseView.extend({
    className: 'tdc-suggestion',

    events: {
      'click a.tdc-apply-consolidation': 'applyConsolidation',
      'click a.tdc-dismiss-suggestion': 'dismiss',
      'click a.tdc-make-primary': 'updatePrimary',
      'click a.tdc-remove-term': 'removeTerm'
    },

    initialize: function(options) {
      BaseView.prototype.initialize.apply(this, arguments);
      this.template = options.template;
      this.group = options.group;
      return this;
    },

    render: function() {
      this.$el.html('');

      var terms = '';

      if (this.group.length <= 1) {
        this.$el.append(this.template({ term: this.group[0] }));
      } else {
        _.each(this.group, function(term, idx) {
          if (idx == 0)
            terms += _.template($('#tdc-primary-term-tmpl').html())({ term: term });
          else
            terms += _.template($('#tdc-secondary-term-tmpl').html())({ term: term });
        });
        this.$el.append(this.template({ terms: terms }));
      }

      return this;
    },

    displayMessage: function(data) {
      this.hideSpinner();
      this.$el.html('');
      this.$el.html('<p>' + data.message + '</p>');
    },

    applyConsolidation: function() {
      var self = this,
      form = this.$el.find('form'),
      term_ids = form.find('input[name="term_ids[]"]').map(function(idx, ele) { return $(ele).val(); }).get(),
      primary_term = form.find('input[name="primary_term_id"]').val();

      this.showSpinner();
      this.request('apply', {
        primary_term: primary_term,
        term_ids: term_ids,
        taxonomy: TDC.taxonomy
      }, function(data) {
        self.displayMessage(data);
        self.controller.suggestion_form.getSuggestions();
      });

      return false;
    },

    dismiss: function() {
      var self = this,
      form = this.$el.find('form'),
      primary_term = form.find('input[name="primary_term_id"]').val();

      this.showSpinner();
      this.request('dismiss', {
        primary_term: primary_term,
        taxonomy: TDC.taxonomy
      }, function(data) {
        self.displayMessage(data);
        self.controller.suggestion_form.getSuggestions();
      });

      return false;
    },

    updatePrimary: function(event) {
      var target = $(event.currentTarget),
      target_term_id = target.data('term-id'),
      index;

      _.each(this.group, function(v, i) {
        if (v.term_id == target_term_id) {
          index = i;
          return;
        }
      });

      var new_primary = this.group.splice(index, 1);
      this.group.unshift(new_primary[0]);
      this.render();

      return false;
    },

    removeTerm: function(event) {
      var target= $(event.currentTarget),
      parent = target.parent().parent().parent();

      parent.remove();
      return false;
    },

    request: function(type, data, success) {
      var self = this;

      if (!type)
        return false;

      if (typeof self.ongoing !== 'undefined' && $.inArray(self.ongoing.state(), ['resolved', 'rejected']) == -1)
        return false;

      var opts = {
        url: ajaxurl,
        data: {
          action: 'tdc_ajax_' + type + '_consolidation_suggestions',
          security: TDC.ajax_nonce,
          request: JSON.stringify(data)
        },
        dataType: 'json',
        method: 'post',
        success: function(data) {
          if (typeof success !== 'undefined')
            success(data);
        }
      };

      self.showSpinner();
      self.ongoing = $.ajax(opts);
      return false;
    }
  });

  $(document).ready(function() {
    TDC.instances.suggestion_list = new SuggestionList({ el: '#tdc-suggestions-list' });
    TDC.instances.generate_button = new GenerateSuggestions({ el: '#tdc-suggestions-request' });
    TDC.instances.tax_selector = new TaxonomySelector({
      el: '#tdc-tax-selector',
      suggestion_list: TDC.instances.suggestion_list
    });

    var initialSetup = function() {
      TDC.instances.suggestion_list.$el.html('');

      if (typeof TDC.instances.suggestion_list.pagination !== 'undefined') {
        TDC.instances.suggestion_list.pagination.hide();
      }

      if (TDC.existing[TDC.taxonomy]) {
        TDC.instances.tax_selector.disable();
        TDC.instances.generate_button.disableButton();
        TDC.instances.generate_button.showSpinner();
        TDC.instances.generate_button.fetchSuggestions();
      }
    };

    TDC.instances.tax_selector.on('taxonomy_changed', function(event) {
      initialSetup();
    });

    initialSetup();
  });

}());
