## Android SDK for ARCaptcha

###### [Installation](#installation) | [Example](#display-a-arcaptcha-challenge)

This SDK provides a native sdk for [ARCaptcha](https://www.arcaptcha.ir). You will need to configure a `site key` and a `secret key` from your arcaptcha account in order to use it.


## Installation

### Gradle
<pre>
// Step 1. Add it in your root settings.gradle at the end of repositories:
dependencyResolutionManagement {
    repositoriesMode.set(RepositoriesMode.FAIL_ON_PROJECT_REPOS)
    repositories {
        mavenCentral()
        maven { url 'https://jitpack.io' }
    }
}
// Step 2. Add the dependency
dependencies {
    implementation 'com.github.arcaptcha:arcaptcha-native-android-sdk:v1.0.6'
}
</pre>

### Maven
```xml
//Step 1. Add to pom.xml
<repositories>
    <repository>
        <id>jitpack.io</id>
        <url>https://jitpack.io</url>
    </repository>
</repositories>

//Step 2. Add the dependency
<dependency>
    <groupId>com.github.arcaptcha</groupId>
    <artifactId>arcaptcha-native-android-sdk</artifactId>
    <version>v1.0.6</version>
</dependency>
```



## Display a Question challenge

The following snippet code will ask the user to complete a Question challenge. 

### XML
```xml
<co.arcaptcha.arcaptcha_native_sdk.containers.QuestionContainerView
    android:id="@+id/mainQuestionContainer"
    android:layout_width="match_parent"
    android:layout_height="wrap_content" />
```

### Kotlin
```kotlin
val questArcApi = ArcaptchaAPI("<YOUR_SITE_KEY>", "<YOUR_DOMAIN>")

mainQuestContainer.initCaptcha(questArcApi, object : CaptchaCallback {
    override fun onCorrectAnswer(token: String) {
        Log.d("Puzzle Token", token)
    }

    override fun onError(errorCode: Int, message: String) {
        Log.d("Puzzle Error", "$message ($errorCode)")
    }

    override fun onWrongAnswer() {
        mainQuestContainer.loadCaptcha()
    }

    override fun onStateChanged(state: CaptchaState) {
        if(state == CaptchaState.LoadingCaptcha) {
        }
    }
})

mainQuestContainer.loadImageCaptcha()
// OR: mainQuestContainer.loadVoiceCaptcha()
```

## Display a Puzzle challenge

The following snippet code will ask the user to complete a Puzzle challenge.

### XML
```xml
<co.arcaptcha.arcaptcha_native_sdk.containers.PuzzleContainerView
    android:id="@+id/puzzleContainer"
    android:layout_width="match_parent"
    android:layout_height="wrap_content" />
```

### Kotlin
```kotlin
val puzzleArcApi = ArcaptchaAPI("<YOUR_SITE_KEY>", "<YOUR_DOMAIN>")

puzzleContainer.initCaptcha(puzzleArcApi, object : CaptchaCallback {
    override fun onCorrectAnswer(token: String) {
        Log.d("Puzzle Token", token)
    }

    override fun onError(errorCode: Int, message: String) {
        Log.d("Puzzle Error", "$message ($errorCode)")
    }

    override fun onWrongAnswer() {
        puzzleContainer.loadCaptcha()
    }

    override fun onStateChanged(state: CaptchaState) {
        if(state == CaptchaState.LoadingCaptcha) {
        }
    }
})

puzzleContainer.loadCaptcha()
```

## Error Codes Summary

| Code | Name               | Type     | Description |
|-----:|--------------------|----------|-------------|
| 101  | CreateServerError  | Server   | Server failed to create captcha |
| 102  | CreateNetworkError | Network  | Network error during captcha creation |
| 201  | AnswerServerError  | Server   | Server failed to validate answer |
| 202  | AnswerNetworkError | Network  | Network error during answer submission |
| 203  | AnswerWrongError   | Logic    | Incorrect captcha answer |
| 401  | UnknownError       | Unknown  | Unclassified error |

## Dark & Light Mode Support
This library supports Light and Dark mode automatically.
All widgets follow the host application's current theme via the provided Context.
When the app switches between Light and Dark mode, the widgets update accordingly without any additional configuration

### Switch theme in your app
```kotlin
// Enable Dark mode
AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_YES)

// Enable Light mode
AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_NO)

// Follow system setting
AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_FOLLOW_SYSTEM)
```

## Display a Question challenge in Jetpack Compose

Add this theme to themes.xml
### XML
```xml
<style name="AppTheme" parent="Theme.MaterialComponents.DayNight.NoActionBar">
    <item name="materialButtonStyle">@style/Widget.MaterialComponents.Button</item>
</style>
```

### Kotlin
```kotlin
@Composable
fun ArcaptchaQuestion(
    siteKey: String,
    domain: String,
    modifier: Modifier = Modifier,
    onCorrect: (token: String) -> Unit,
    onError: (errorCode: Int, message: String) -> Unit
) {
    AndroidView(
        modifier = modifier,
        factory = { ctx ->
            val themedContext = ContextThemeWrapper(ctx, R.style.AppTheme)

            val container = QuestionContainerView(themedContext).apply {
                val api = ArcaptchaAPI(siteKey, domain)
                initCaptcha(api, object : CaptchaCallback {
                    override fun onCorrectAnswer(token: String) {
                        onCorrect(token)
                    }

                    override fun onWrongAnswer() {
                        loadCaptcha()
                    }

                    override fun onError(errorCode: Int, message: String) {
                        onError(errorCode, message)
                    }

                    override fun onStateChanged(state: CaptchaState) {}
                })

                loadImageCaptcha()
            }

            container
        },
        update = { view -> }
    )
}

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            MyApplicationTheme {
                Scaffold(modifier = Modifier.fillMaxSize()) { innerPadding ->
                    ArcaptchaQuestion(
                        siteKey = "<YOUR_SITE_KEY>",
                        domain = "<YOUR_DOMAIN>",
                        onCorrect = { token -> },
                        onError = { errorCode, message -> }
                    )
                }
            }
        }
    }
}
```

