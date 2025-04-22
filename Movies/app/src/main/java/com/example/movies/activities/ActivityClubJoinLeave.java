package com.example.movies.activities;

import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.movies.R;
import com.example.movies.models.ClubResponse;
import com.example.movies.network.ClubApiService;
import com.example.movies.network.RetrofitClient;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ActivityClubJoinLeave extends AppCompatActivity {

    ClubApiService apiService;

    Spinner clubSpinner;
    EditText editStudentId, editPassword;
    TextView tvMessage;
    Button btnJoin, btnLeave;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_join_leave_club);
        apiService = RetrofitClient.getInstance().create(ClubApiService.class);
        initializeViews();
        setButtonListeners();
    }

    private void initializeViews() {
        clubSpinner = findViewById(R.id.club_spinner);
        editStudentId = findViewById(R.id.editStudentId);
        editPassword = findViewById(R.id.editPassword);
        tvMessage = findViewById(R.id.tvMessage);
        btnJoin = findViewById(R.id.btnJoin);
        btnLeave = findViewById(R.id.btnLeave);
        loadClubDropdown();
    }

    private void setButtonListeners() {
        btnJoin.setOnClickListener(v -> JoinClub());
        btnLeave.setOnClickListener(v -> LeaveClub());
    }

    private void loadClubDropdown() {
        apiService.getClubs("get_clubs").enqueue(new Callback<ClubResponse>() {
            @Override
            public void onResponse(Call<ClubResponse> call, Response<ClubResponse> response) {
                //Log.d("API_RESULT", response.body().toString());
                if (response.isSuccessful() && response.body() != null && response.body().isSuccess()) {
                    List<String> clubs = new ArrayList<>();
                    clubs.add("--Select a club--");
                    clubs.addAll(response.body().clubs);

                    ArrayAdapter<String> adapter = new ArrayAdapter<>(
                            ActivityClubJoinLeave.this,
                            android.R.layout.simple_spinner_item,
                            clubs
                    );
                    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                    clubSpinner.setAdapter(adapter);
                } else {
                    Toast.makeText(ActivityClubJoinLeave.this, "Failed to load clubs", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<ClubResponse> call, Throwable t) {
                Toast.makeText(ActivityClubJoinLeave.this, "API error: " + t.getMessage(), Toast.LENGTH_LONG).show();
            }
        });
    }

    private boolean isFormValid(String club, String studentId, String password) {
        if (club.equals("--Select a club--")) {
            showMessage("Please select a club.", Color.RED);
            return false;
        }

        if (studentId.isEmpty()) {
            showMessage("Please enter your student ID.", Color.RED);
            return false;
        }

        if (password.isEmpty()) {
            showMessage("Please enter your password.", Color.RED);
            return false;
        }

        return true;
    }

    private void JoinClub() {
        String club = clubSpinner.getSelectedItem().toString();
        String studentId = editStudentId.getText().toString().trim();
        String password = editPassword.getText().toString().trim();

        if (!isFormValid(club, studentId, password)) return;



        Call<ClubResponse> call = apiService.joinClub(club, studentId, password);

        call.enqueue(new Callback<ClubResponse>() {
            @Override
            public void onResponse(Call<ClubResponse> call, Response<ClubResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    ClubResponse result = response.body();
                    int color = result.isSuccess() ? Color.GREEN : Color.RED;
                    showMessage(result.getMessage(), color);
                } else {
                    showMessage("Unexpected server response.", Color.RED);
                }
            }

            @Override
            public void onFailure(Call<ClubResponse> call, Throwable t) {
                showMessage("Failed to connect: " + t.getMessage(), Color.RED);
            }
        });
    }



    private void LeaveClub() {
        String club = clubSpinner.getSelectedItem().toString();
        String studentId = editStudentId.getText().toString().trim();
        String password = editPassword.getText().toString().trim();

        if (!isFormValid(club, studentId, password)) return;


        Call<ClubResponse> call = apiService.leaveClub(club, studentId, password);
        call.enqueue(new Callback<ClubResponse>() {
            @Override
            public void onResponse(Call<ClubResponse> call, Response<ClubResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    ClubResponse result = response.body();
                    int color = result.isSuccess() ? Color.BLUE : Color.RED;
                    showMessage(result.getMessage(), color);
                } else {
                    showMessage("Unexpected server response.", Color.RED);
                }
            }

            @Override
            public void onFailure(Call<ClubResponse> call, Throwable t) {
                showMessage("Failed to connect: " + t.getMessage(), Color.RED);
            }
        });
    }


    private void showMessage(String message, int color) {
        tvMessage.setTextColor(color);
        tvMessage.setText(message);
    }
}
