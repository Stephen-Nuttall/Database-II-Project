package com.example.movies.network;

import com.example.movies.models.ClubResponse;

import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;

public interface ClubApiService {

    @FormUrlEncoded
    @POST("join_club.php")
    Call<ClubResponse> joinClub(
            @Field("club_name") String clubName,
            @Field("student_id") String studentId,
            @Field("password") String password
    );

    @FormUrlEncoded
    @POST("join_club.php")
    Call<ClubResponse> getClubs(@Field("action") String action);

    @FormUrlEncoded
    @POST("leave_club.php")
    Call<ClubResponse> leaveClub(
            @Field("club_name") String clubName,
            @Field("student_id") String studentId,
            @Field("password") String password
    );

    @FormUrlEncoded
    @POST("create_club.php")
    Call<ClubResponse> createClub(
            @Field("instructor_id") String instructorId,
            @Field("password") String password,
            @Field("club_name") String clubName,
            @Field("president_id") String presidentId
    );
}
