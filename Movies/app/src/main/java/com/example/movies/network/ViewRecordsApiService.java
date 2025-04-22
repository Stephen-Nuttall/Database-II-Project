package com.example.movies.network;

import com.example.movies.models.RecordsResponse;

import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;

public interface ViewRecordsApiService {
    @FormUrlEncoded
    @POST("item5.php")
    Call<RecordsResponse> viewRecords(
            @Field("instructor_id") String instructorId,
            @Field("password") String password
    );
}
