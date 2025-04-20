package com.example.movies.network;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class RetrofitClient {

    //Replace it with your ip and port , so it can work
    private static final String BASE_URL = "http://10.0.2.2:1234/Database-II-Project/phase%203/club_api/";

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
