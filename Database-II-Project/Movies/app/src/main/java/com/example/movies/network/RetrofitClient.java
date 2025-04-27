package com.example.movies.network;


import com.example.movies.BuildConfig;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class RetrofitClient {

    //Replace it with your ip and port , so it can work
<<<<<<<< HEAD:Movies/app/src/main/java/com/example/phaseIII/network/RetrofitClient.java
    private static final String BASE_URL = BuildConfig.BASE_URL;;
========
    private static final String BASE_URL = "http://xx.xxx.xx.xxx/Database-II-Project/phase%203/";
>>>>>>>> working-version:Database-II-Project/Movies/app/src/main/java/com/example/movies/network/RetrofitClient.java

    private static Retrofit retrofit = null;

    public static Retrofit getInstance() {
        if (retrofit == null) {
            retrofit = new Retrofit.Builder()
                    .baseUrl(BASE_URL)
                    .addConverterFactory(GsonConverterFactory.create()) // for JSON parsing
                    .build();
        }
        return retrofit;
    }
}
