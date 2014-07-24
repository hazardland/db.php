db.php - represents Code First style ORM database framework
================

Visit http://dbphp.net for more info as readme.md update is in progress.

1. It reads your class definitions and creates/modifies databases/tables/fields
according extracted data from classes and its properties.
2. To collect additional info it uses doc comments.
3. Supports relations between classes and its properties one to one, one to many and many to many.
4. Has extendable caching engine. Uses apc_cache for long cache type by default and session for user type cache
5. Supports localization. Just one directive to localize field and it creates and handles fields for localized values