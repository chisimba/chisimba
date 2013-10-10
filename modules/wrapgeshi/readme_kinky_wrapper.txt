This is a KINKY wrapper for the geshi syntax hilighting class. Put all the PHP files from geshi updates into the lib folder. Put the geshi docs into the docs folder.

Dear Colleagues,

I have written a wrapper for the geshi code highlighting class. It is useful for everyone to begin thinking in design patterns, so this is an example of the adapter pattern, and it is ALWAYS the correct way to wrap a third-party class into KEWL.NextGen because it does not require any changes to the underlying class. As a consequence, any upgrades to the underlying class can be done without any change to the wrapper, thus avoiding getting us locked into using old versions of classes. I propose that any modules that are based on this kind of design pattern begins with wrap, hence the module is called wrapgeshi. This says clearly that it is a wrapper module, and avoids name conflicts with the underlying class.

This module and its class (geshiwrapper) will highlight code from most commonly used languages. I have abstracted the most useful functionality. If anyone ever needs more, then feel free to wrap other functions as well. Note that I have converted the geshi naming from the under_score_form to the KNG camelCaseForm. If you want to generate syntax-highlighted code for inclusion in documentation you can use wrapgeshi, which has an input box for entering code as well as the line number to start numbering from. I have also updated the viewsource module to use the geshiwrapper class.

Next step is to build code parser that recognizes [CODE LANGUAGE=php] [/CODE] sntax so that code in text can be correctly highlighted.



Regards
Derek