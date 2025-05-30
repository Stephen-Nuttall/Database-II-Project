package com.example.movies.network;

import com.example.movies.models.Responses;

import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;

public interface ApiService {

    @FormUrlEncoded
    @POST("item2.php")
    Call<Responses> getSections(@Field("action") String action);

    @FormUrlEncoded
    @POST("item2.php")
    Call<Responses> enroll(
            @Field("password") String password,
            @Field("student_id") String studentId,
            @Field("section") String section
    );

    @FormUrlEncoded
    @POST("item3.php")
    Call<Responses> viewStudentRecords(
            @Field("student_id") String studentId,
            @Field("password") String password
    );

    @FormUrlEncoded
    @POST("item4.php")
    Call<Responses> viewRecords(
            @Field("instructor_id") String instructorId,
            @Field("password") String password
    );


    @FormUrlEncoded
    @POST("join_club.php")
    Call<Responses> joinClub(
            @Field("club_name") String clubName,
            @Field("student_id") String studentId,
            @Field("password") String password
    );

    @FormUrlEncoded
    @POST("join_club.php")
    Call<Responses> getClubs(@Field("action") String action);

    @FormUrlEncoded
    @POST("leave_club.php")
    Call<Responses> leaveClub(
            @Field("club_name") String clubName,
            @Field("student_id") String studentId,
            @Field("password") String password
    );

    @FormUrlEncoded
    @POST("create_club.php")
    Call<Responses> createClub(
            @Field("instructor_id") String instructorId,
            @Field("password") String password,
            @Field("club_name") String clubName,
            @Field("president_id") String presidentId
    );
}
