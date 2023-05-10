<?php
/**
 * The function adds a "Quote text and reply" link to the admin links of topics and replies in a
 * bbPress forum.
 * 
 * @param links The  parameter is an array of links that are displayed in the topic or reply
 * admin links section.
 * @param topic_id The ID of the topic being displayed.
 * 
 * @return an array of links to be added to the admin links section of a bbPress topic or reply.
 * Specifically, it is adding a link with the text "Quote text and reply" and a class of
 * "quote_text_and_reply".
 */
function sample_add_bbp_admin_link($links, $topic_id)
{

    $links['quote_text'] = '<a href="#new-post" class="quote_text_and_reply">Quote text and reply</a>';

    return $links;
}
add_filter('bbp_topic_admin_links', 'sample_add_bbp_admin_link', 10, 2);
add_filter('bbp_reply_admin_links', 'sample_add_bbp_admin_link', 10, 2);


/**
 * The function adds a jQuery script to the WordPress footer that allows users to quote and reply to a
 * specific text in a forum thread.
 */
add_action('wp_footer', 'quote_text_and_reply');
function quote_text_and_reply()
{ ?>
    <script>
        jQuery(document).ready(function() {
            jQuery(document).on('click', '.quote_text_and_reply', function() {
                // var abc = jQuery('.bbp-reply-content p').text();
                // var parent = jQuery(this).parent().parent().find('p:not(.wpulike)').text();
                var parent = jQuery(this).parent().parent().parent().parent().parent().find('.bbp-reply-content').find('p:not(.wpulike)').text();

                console.log(parent);
                tinymce.activeEditor.setContent(parent);
            });
        });

        jQuery.fn.quoteHighlighted = function(options) {
            var settings = {},
                classes;
            options = options || {};

            // html DOM element to use as a button
            settings.node = options.node || '<button type="button">Quote</button>';
            // css class to add to the html node
            settings.cssClass = options.cssClass || "quote-me";
            // minimum length of the selected text for the quote button to appear
            settings.minLength = options.minLength || 1;
            // maximum length of the selected text for the quote button to appear
            settings.maxLength = options.maxLength || 144 * 4;
            // extra content to attach (mostly used to add URLs)
            settings.extra = options.extra || "";
            // twitter 'via' handle
            // (basically appends 'via @twitterhandle' to the quote)
            settings.via = options.via || null;
            // arguments to pass to the window.open() function
            settings.popupArgs =
                options.popArgs || "width=400,height=400,toolbar=0,location=0";
            // defines a callback function to pass text to when a user takes action
            settings.callback = options.callback || null;

            // get an array of classes filtering out empty whitespaces
            classes = settings.cssClass.split(" ").filter(function(item) {
                return item.length;
            });
            settings._selector = "." + classes.join(".");

            // event that fires when any non-empty text is selected
            var onTextSelect = function(selector, callback) {

                function getSelectedText() {
                    if (window.getSelection) {
                        return window.getSelection().toString();
                    } else if (document.selection) {
                        return document.selection.createRange().text;
                    }
                    return "";
                }

                // fires the callback when text is selected
                jQuery(selector).mouseup(function(e) {
                    var text = getSelectedText();
                    if (text !== "") {
                        callback(e, text);
                    }
                });

                // removes the button when the selected text loses focus
                jQuery(document).click(function(e) {
                    var text = getSelectedText();
                    if (text !== "") {
                        e.stopPropagation();
                    } else jQuery(settings._selector).fadeOut(500).remove();
                });
            };

            // var getquoteURL = function(text, extra, via) {
            //     var url = jQuery(location).attr('href') + '#new-post';
            //     url += encodeURIComponent(text);

            //     if (extra) url += encodeURIComponent(" " + extra);

            //     if (via) url += "&via=" + via;

            //     return url;
            // };

            onTextSelect(this, function(e, text) {
                var btnExists = jQuery(settings._selector).length,
                    url;

                if (
                    btnExists ||
                    text.length > settings.maxLength ||
                    text.length < settings.minLength
                )
                    return;

                // url = getquoteURL(text, settings.extra, settings.via);

                jQuery(settings.node)
                    .addClass(settings.cssClass)
                    .offset({
                        top: e.pageY,
                        left: e.pageX
                    })
                    .css({
                        position: "absolute",
                        cursor: "pointer"
                    })
                    .appendTo("body")
                    .fadeIn(500)
                    .click(function(e) {
                        jQuery(settings._selector).fadeOut(500).remove();
                        // Open the quote window
                        // window.open(url);
                        jQuery('html, body').animate({
                            scrollTop: jQuery('#new-post').offset().top
                        }, 2000);
                        tinymce.activeEditor.setContent(text);
                        // Notify the callback function if defined
                        if (settings.callback != null) {
                            settings.callback(text);
                        }
                    });
            });
        };

        jQuery(".bbp-reply-content").quoteHighlighted({
            node: '<a href="#" class="w-button quote">Quote</a>',
            minLength: 2,
            maxLength: 240 * 2,
            extra: "https://webflow.com",
            via: "waldobroodryk",
            popupArgs: "width=600,height=600,toolbar=0,location=0",
        });
    </script>
<?php
}
