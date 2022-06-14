# ig-story-api-php
Simple PHP Script that gives an array of instagram stories with their URLs from the User ID.

This only works for public accounts, unless you use an account that follows the account whose stories you want to get, and remove the private account check in getStories().

# Install
1. Clone this Project.
2. Rename example.config.php to config.php.
3. Fill in the sessionid and ds_user_id from your Cookie.
4. Use ``require_once("api.php");`` to import into your php script.

# Functions

## instagramRequest($url)
Makes a request to the $url using your Cookies and a User Agent that Instagram allows and returns the response content.

## getUserId($username)
Returns the User ID from a Username.

## isPrivate($username)
Returns if the User is private.

## getStories($user_id)
Returns an array of stories from the User ID.
If the Story is an Image preview_image and final_img_vid will be the same.

Array Format:

```php
array(3) {
  ["error"]=>
  bool(false)
  ["count"]=>
  int(1)
  ["stories"]=>
  array(1) {
    [0]=>
    array(5) {
      ["upload_time"]=>
      int(1655189222)
      ["expire_time"]=>
      int(1655275622)
      ["media_type"]=>
      string(5) "video"
      ["preview_image"]=>
      string(17) "PREVIEW IMAGE URL"
      ["final_img_vid"]=>
      string(17) "CONTENT IMAGE URL"
    }
  }
}
```

## convertStoryObject($story_object)
Converts the Instagram Story object the story element returned by getStories()