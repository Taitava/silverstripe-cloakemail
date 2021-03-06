silverstripe-cloakemail
=======================

Module to easily encode email addresses inside templates, page content and PHP code so that SPAM bots can't understand them, but human readers see them clearly.


## Maintainer Contact

 Jarkko Linnanvirta
 posti (at) taitavasti (dot) fi (in English or in Finnish)
 www.taitavasti.fi (only in Finnish)

## Requirements

- SilverStripe 4.0.0 or greater
- PHP extensions: `json`
- jQuery (unless you use the `nojs` `mode`, but it's ugly)

## Installation

The recommended way to install this module is by using `composer`:

```
composer require "taitava/silverstripe-cloakemail:*"
```

## Documentation

This module makes it easy to cloak email addresses in webpage content and templates to prevent SPAM bots from getting them. The module uses JavaScript + jQuery to reveal the email addresses, although this can be turned off if you don't wish to bloat your website with JavaScript.

The following datatypes are extended to be able to cloak email addresses: text, varchar and enum. To cloak an email address in a template file, just trail the variable containing the email address with .Cloak().

For example:

	$SiteConfig.ContactEmailAddress.Cloak()
	
You can also use $Cloak('hide.my@email.address') inside a template (only inside a Page context).

Email addresses in Page objects' contents are cloaked automatically, but only if page_content is set to true in cloakemail.yml.

## Configuration

*app/_config/cloakemail.yml*

	---
	after: cloakemail
	---
	Taitava\CloakEmail\CloakEmail:
	  mode: simple
	  convert_page_content: true
	  page_insert_links: false
	  template_insert_links: false
	  purge_mailto_links: true
      at: ' [a] '
      dot: ' [dot] '
      hard_noscript_error: 'JavaScript must be turned on in order to see this email address'
	  
### mode

There are multiple cloaking modes:
 - hard: Impossible for SPAM bots to get around if they can't interpret JavaScript. Without JavaScript support, the user cannot see the email address in any form.
 - simple: Cloaks the email address in a human readable way: my.name@my.place becomes to something like my (dot) name (a) my (dot) place. However, JavaScript is used to reverse it to the original form to make it user friendly in the browser. If JavaScript is turned off, the user can still see it in the cloaked format. This is the best compromise for fallback ability, user friendliness and performance.
 - nojs: Same as 'simple', but no JavaScript is used.
 - none: Makes no changes to email addresses. Good for debugging.
 
Default: 'simple'
 
### convert_page_content

If true, CloakEmail processes page content and cloaks all email addresses in Page objects' contents automatically.

WARNING! This feature can easily break things. A simple email address just laying around between text should work just fine, but if an email address resides inside an HTML tag (for example <a href="mailto:send.mail@to.me">Feedback</a>) it will break the tag because it fills in its own HTML code! Exception: [i]nojs[/i] mode does not fill in any HTML code unless you have [i]page_insert_links[/i] set to true.

Default: false

### page_insert_links and template_insert_links

If true, wraps cloaked email addresses inside mailto-links (<a href="mailto:*CLOAKED ADDRESS*">*CLOAKED ADDRESS*</a>). However, this is not done if [i]mode[/i] is set to [i]none[/i].

Default: false for both variables

### purge_mailto_links

The purpose of this option is to prevent malformed HTML code from being generated if set to true and if the source HTML content contains email address links like `<a href="mailto:email@address.com">email@address.com</a>`. If this option is off, the module would convert the email address in the `href` attribute in a way that would insert HTML inside the attribute, which would simply break the outputted HTML.

This option provides a "quirky" workaround to this problem by removing all mailto links from the HTML content before starting to process it. So it replaces the link with the email address found from the `href` attribute. **Please note that the anchor text of the link is not preserved in any way!**

You can use page_insert_links and/or template_insert_links options to recreate the mailto links, but you still can't get the original anchor texts back, instead the email address itself will be used as the anchor text - which is usually correct.

So while this feature does not perfectly fix the bug that it relates to, at least it offers a better-than-nothing solution. If you want to start a discussion about this topic, please see the issue #3 . Please do not hesitate to ask if you have any questions about this feature/bug! :)

Default: false

### at and dot

@ and . will be replaced with these strings in email addresses when mode is either 'simple' or 'nojs'. In 'hard' mode these are not used, because the email addresses get totally scrambled in that mode. HTML code is allowed here.

Defaults: ' at ' and ' dot '

### hard_noscript_error

Error message that will be shown if JavaScript is turned off. This only affects 'hard' mode. The message appears in the place of the email address.

Default: 'JavaScript must be turned on in order to see this email address.'

## TODO

Here is a list of some ideas. I make no promises about future development, but I gadly welcome pull requests if you want to implement these or your own ideas! :)

- Improve the performance of cloaking email addresses in Page Objects' contents.
- Make it possible to change [i]mode[/i] and [i]insert_link[/i] settings temporarily on .Cloak() calls in templates.
- Create a translation file and move [i]hard_noscript_error[/i] there
- Write a bit more JavaScript and drop jQuery to make the module lighter.

