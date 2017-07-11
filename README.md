PHP Number to Words By Gomersol Technologies (http://gomersol.com)
==================================================================================================

This library will allows you to spell various numeric values from 0 to 999,999,999,999.00 in the various languages. For now the library is only supporting the English Language.
In the next few monts we hope to add support for Swahili,French, German, Spanish.

The library was prepared to help with displaying the spelling of various numbers when printing on cheques. Please highlight other use cases on the comments and submits any issues
you get while using this library. Godspeed.

For license information check the [LICENSE](LICENSE.md)-file.

**joke** 
If only president Zuma has his speech writers using this class, the whole [President Zuma ANC Numbers Challenge](https://www.youtube.com/watch?v=nqNa6992ih4) Would not have happenned.**lol**

You can also access some of my other repositories on my [Packagist Profile](https://packagist.org/packages/chitwarnold/). 

Installation
------------

The preferred way to install this library is through [composer](http://getcomposer.org/download/).

Either run

```
composer require chitwarnold/n2w
```

or add

```json
"chitwarnold/ais": "~1.0.4",
```

to the require section of your composer.json.


Demonstration
---------------

```
#import the libraries
use chitwarnold\n2w\en\N2w;
use chitwarnold\n2w\en\readers\N2wReaders;


# get some spellcheck done
$_spelling_bee = new N2w();
$decimal_point  = 2;
$start = 100;
$stop = 200;
echo "++++++++++++++++++++++++++++++++++++++++++++++++++++++".PHP_EOL;
echo "| Range Counting challenge From $start to $stop |".PHP_EOL;
echo "++++++++++++++++++++++++++++++++++++++++++++++++++++++".PHP_EOL;


for($i = $start; $i <= $stop; $i++)
{
    $spell = $_spelling_bee->updateChallenge($i,$decimal_point)->spell();
    echo "$i."." ".$spell.PHP_EOL;
}

```
