/**
 * SusanCarrier.com - SEO & Performance Analytics Tracker
 * Handles Core Web Vitals (LCP, FID/INP, CLS, TTFB, FCP), Call/Lead Conversion Tracking,
 * and In-Browser Performance Diagnostics.
 */
(function () {
  'use strict';

  var metrics = {
    ttfb: 0,
    fcp: 0,
    lcp: 0,
    cls: 0,
    inp: 0,
    domReady: 0,
    pageLoad: 0
  };

  // Helper to send events safely to Google Analytics (gtag / dataLayer)
  function sendEvent(eventName, params) {
    params = params || {};
    if (typeof window.gtag === 'function') {
      window.gtag('event', eventName, params);
    } else if (window.dataLayer && Array.isArray(window.dataLayer)) {
      window.dataLayer.push({
        event: eventName,
        ...params
      });
    }
  }

  // 1. Navigation & Page Load Timings
  window.addEventListener('load', function () {
    setTimeout(function () {
      if (window.performance && performance.getEntriesByType) {
        var navEntries = performance.getEntriesByType('navigation');
        if (navEntries && navEntries.length > 0) {
          var nav = navEntries[0];
          metrics.ttfb = Math.round(nav.responseStart - nav.requestStart);
          metrics.domReady = Math.round(nav.domContentLoadedEventEnd - nav.startTime);
          metrics.pageLoad = Math.round(nav.loadEventEnd - nav.startTime);

          sendEvent('perf_page_load', {
            event_category: 'Web Performance',
            ttfb_ms: metrics.ttfb,
            dom_ready_ms: metrics.domReady,
            load_time_ms: metrics.pageLoad
          });
        }
      }
    }, 0);
  });

  // 2. Core Web Vitals Tracking (PerformanceObserver)
  if ('PerformanceObserver' in window) {
    // First Contentful Paint (FCP)
    try {
      var fcpObserver = new PerformanceObserver(function (entryList) {
        var entries = entryList.getEntriesByName('first-contentful-paint');
        if (entries.length > 0) {
          metrics.fcp = Math.round(entries[0].startTime);
          sendEvent('perf_fcp', {
            event_category: 'Core Web Vitals',
            value: metrics.fcp,
            rating: metrics.fcp <= 1800 ? 'good' : (metrics.fcp <= 3000 ? 'needs-improvement' : 'poor')
          });
          fcpObserver.disconnect();
        }
      });
      fcpObserver.observe({ type: 'paint', buffered: true });
    } catch (e) {}

    // Largest Contentful Paint (LCP)
    try {
      var lcpObserver = new PerformanceObserver(function (entryList) {
        var entries = entryList.getEntries();
        var lastEntry = entries[entries.length - 1];
        if (lastEntry) {
          metrics.lcp = Math.round(lastEntry.startTime);
        }
      });
      lcpObserver.observe({ type: 'largest-contentful-paint', buffered: true });

      window.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden' && metrics.lcp > 0) {
          sendEvent('perf_lcp', {
            event_category: 'Core Web Vitals',
            value: metrics.lcp,
            rating: metrics.lcp <= 2500 ? 'good' : (metrics.lcp <= 4000 ? 'needs-improvement' : 'poor')
          });
        }
      });
    } catch (e) {}

    // Cumulative Layout Shift (CLS)
    try {
      var clsValue = 0;
      var clsObserver = new PerformanceObserver(function (entryList) {
        var entries = entryList.getEntries();
        for (var i = 0; i < entries.length; i++) {
          if (!entries[i].hadRecentInput) {
            clsValue += entries[i].value;
          }
        }
        metrics.cls = Number(clsValue.toFixed(4));
      });
      clsObserver.observe({ type: 'layout-shift', buffered: true });

      window.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden' && metrics.cls > 0) {
          sendEvent('perf_cls', {
            event_category: 'Core Web Vitals',
            value: metrics.cls,
            rating: metrics.cls <= 0.1 ? 'good' : (metrics.cls <= 0.25 ? 'needs-improvement' : 'poor')
          });
        }
      });
    } catch (e) {}
  }

  // 3. Conversion Tracking (Phone Clicks, Form Submits, File Downloads)
  document.addEventListener('DOMContentLoaded', function () {
    // Track Phone number clicks
    document.addEventListener('click', function (e) {
      var target = e.target.closest('a');
      if (!target) return;

      var href = target.getAttribute('href') || '';

      // Phone call clicks
      if (href.indexOf('tel:') === 0) {
        sendEvent('phone_call_click', {
          event_category: 'Conversions',
          event_label: href.replace('tel:', ''),
          page_location: window.location.href
        });
      }

      // PDF / File Downloads
      if (href.match(/\.(pdf|zip|docx?|xlsx?)$/i)) {
        sendEvent('file_download', {
          event_category: 'Engagement',
          file_name: href.split('/').pop(),
          page_location: window.location.href
        });
      }
    });

    // Track Contact Form submissions
    var forms = document.querySelectorAll('form');
    forms.forEach(function (form) {
      form.addEventListener('submit', function () {
        sendEvent('generate_lead', {
          event_category: 'Conversions',
          form_id: form.id || 'contact_form',
          page_location: window.location.href
        });
      });
    });
  });

  // 4. In-Browser SEO & Performance Diagnostic Tool
  window.getSeoPerformanceReport = function () {
    var title = document.title || 'Missing';
    var descEl = document.querySelector('meta[name="description"]');
    var desc = descEl ? descEl.getAttribute('content') : 'Missing';
    var canonicalEl = document.querySelector('link[rel="canonical"]');
    var canonical = canonicalEl ? canonicalEl.getAttribute('href') : 'Missing';
    var h1s = Array.from(document.querySelectorAll('h1')).map(function (h) { return h.innerText.trim(); });
    var schemaEl = document.querySelector('script[type="application/ld+json"]');

    var lcpRating = metrics.lcp <= 2500 ? '🟢 Good (<=2.5s)' : (metrics.lcp <= 4000 ? '🟡 Needs Improvement' : '🔴 Poor (>4.0s)');
    var clsRating = metrics.cls <= 0.1 ? '🟢 Good (<=0.1)' : (metrics.cls <= 0.25 ? '🟡 Needs Improvement' : '🔴 Poor (>0.25)');
    var fcpRating = metrics.fcp <= 1800 ? '🟢 Good (<=1.8s)' : (metrics.fcp <= 3000 ? '🟡 Needs Improvement' : '🔴 Poor (>3.0s)');

    console.group('📊 SusanCarrier.com SEO & Performance Report');
    console.table({
      'Page Title': { Status: title !== 'Missing' ? '🟢 OK' : '🔴 Missing', Value: title.substring(0, 60) },
      'Meta Description': { Status: desc !== 'Missing' ? '🟢 OK' : '🔴 Missing', Value: desc.substring(0, 80) + '...' },
      'Canonical Tag': { Status: canonical !== 'Missing' ? '🟢 OK' : '🔴 Missing', Value: canonical },
      'H1 Tag Count': { Status: h1s.length === 1 ? '🟢 OK (1)' : '🟡 ' + h1s.length + ' found', Value: h1s.join(' | ') },
      'Schema JSON-LD': { Status: schemaEl ? '🟢 Present' : '🔴 Missing', Value: schemaEl ? 'Structured Data Loaded' : 'None' },
      'TTFB (Server Response)': { Status: metrics.ttfb < 800 ? '🟢 Fast' : '🟡 Slow', Value: metrics.ttfb + ' ms' },
      'FCP (First Contentful)': { Status: fcpRating, Value: metrics.fcp + ' ms' },
      'LCP (Largest Contentful)': { Status: lcpRating, Value: (metrics.lcp || 'Calculating...') + ' ms' },
      'CLS (Layout Shift)': { Status: clsRating, Value: metrics.cls }
    });
    console.log('💡 Tip: Call conversions (tel: links) and form leads are automatically tracked and dispatched to your analytics.');
    console.groupEnd();

    return {
      seo: { title: title, description: desc, canonical: canonical, h1Count: h1s.length, schemaPresent: !!schemaEl },
      vitals: metrics
    };
  };
})();
